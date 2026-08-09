<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-200 leading-tight">
            {{ __('Storage Manager (Microservices Engine)') }}
        </h2>
    </x-slot>

    <div class="relative min-h-[calc(100vh-80px)] py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="pointer-events-none absolute inset-x-0 top-0 -z-0 h-64 bg-gradient-to-b from-indigo-500/[0.05] to-transparent"></div>

        <!-- Toast Notification Container (in-page, tidak akan diblokir seperti alert()/confirm()) -->
        <div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-2 w-80 max-w-[90vw]"></div>

        <!-- CSRF Token Meta -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Current folder context (dipakai oleh JS) -->
        <meta id="current-folder-meta" data-folder-id="{{ $currentFolder->id ?? '' }}">

        @if (session('success'))
            <div id="flash-success" class="hidden">{{ session('success') }}</div>
        @endif

        <!-- Drag & Drop Upload Zone Card -->
        <div class="bg-slate-900/80 backdrop-blur-sm border border-slate-800/80 overflow-hidden shadow-xl shadow-black/20 sm:rounded-2xl p-6 text-slate-100">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-bold">Upload Document / File</h3>
                    <p class="text-xs text-slate-400">Mendukung semua format file tanpa batasan ukuran (Chunked Resumable Engine)</p>
                </div>
                <span class="text-xs bg-indigo-950 text-indigo-400 border border-indigo-800 px-3 py-1 rounded-full font-mono">Node: {{ config('app.storage_node', 'node-1') }}</span>
            </div>

            <!-- Drop Zone -->
            <div id="drop-zone" class="group border-2 border-dashed border-slate-700/90 hover:border-indigo-500/80 rounded-2xl p-10 text-center cursor-pointer transition-all duration-300 bg-slate-950/50 hover:bg-indigo-950/10 flex flex-col items-center justify-center space-y-3">
                <div class="w-16 h-16 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center mb-2 group-hover:scale-105 group-hover:bg-indigo-500/15 transition-transform">
                    <svg class="w-9 h-9 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                </div>
                <p class="text-sm font-medium text-slate-300">Tarik & Lepas file ke sini, atau <span class="text-indigo-400 underline">Pilih File</span></p>
                <p class="text-xs text-slate-500">Word, PDF, Images, ISO, Video, Archives (No Size Limit)</p>
                <input type="file" id="file-input" class="hidden" multiple>
            </div>

            <!-- Aksi tambahan: upload folder utuh & buat folder baru -->
            <div class="flex flex-wrap items-center gap-2 mt-4">
                <button type="button" id="upload-folder-btn" class="inline-flex items-center gap-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 px-4 py-2 text-xs font-semibold text-slate-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"></path>
                    </svg>
                    Upload Folder
                </button>
                <input type="file" id="folder-input" class="hidden" webkitdirectory directory multiple>

                <button type="button" id="new-folder-btn" class="inline-flex items-center gap-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 px-4 py-2 text-xs font-semibold text-slate-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m3-3H9m10-7H5a2 2 0 00-2 2v12a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2z"></path>
                    </svg>
                    Folder Baru
                </button>

                <span class="text-[11px] text-slate-500">
                    File/folder akan masuk ke:
                    <strong class="text-slate-300">{{ $currentFolder->name ?? 'Root' }}</strong>
                </span>
            </div>

            <!-- Selected Files Preview (before upload) -->
            <div id="selected-files-wrapper" class="hidden mt-6 space-y-3">
                <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wide">File Terpilih</h4>
                <ul id="selected-files-list" class="space-y-2"></ul>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" id="cancel-selection-btn" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-300 bg-slate-800 hover:bg-slate-700 transition">Batal</button>
                    <button type="button" id="submit-upload-btn" class="px-5 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 shadow-lg shadow-indigo-600/10 transition">Upload File</button>
                </div>
            </div>

            <!-- Upload Progress Monitor -->
            <div id="progress-wrapper" class="hidden mt-6 space-y-3 bg-slate-950 p-4 rounded-lg border border-slate-800">
                <div class="flex items-center justify-between text-xs text-slate-300">
                    <span id="upload-filename" class="font-semibold truncate max-w-xs">Uploading...</span>
                    <span id="upload-percentage" class="font-mono text-indigo-400">0%</span>
                </div>
                <div class="w-full bg-slate-800 rounded-full h-2.5 overflow-hidden">
                    <div id="progress-bar" class="bg-indigo-600 h-2.5 rounded-full transition-all duration-200" style="width: 0%"></div>
                </div>
                <div class="flex justify-between text-[10px] text-slate-500">
                    <span id="upload-status">Memproses potongan file...</span>
                    <span id="upload-speed">Chunked Upload Active</span>
                </div>
            </div>
        </div>

        <!-- File List & Filter Section -->
        <div class="bg-slate-900/80 backdrop-blur-sm border border-slate-800/80 overflow-hidden shadow-xl shadow-black/20 sm:rounded-2xl p-6 text-slate-100 space-y-4">

            <!-- Breadcrumb -->
            <nav class="flex items-center flex-wrap gap-1 text-xs text-slate-400">
                <a href="{{ route('storage.index', array_filter(['search' => request('search')])) }}"
                   class="flex items-center gap-1 px-2 py-1 rounded-lg hover:bg-slate-800 hover:text-slate-200 transition {{ !$currentFolder ? 'text-indigo-400 font-semibold' : '' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Root
                </a>
                @foreach ($breadcrumbs as $crumb)
                    <span class="text-slate-700">/</span>
                    <a href="{{ route('storage.index', array_filter(['folder' => $crumb->id, 'search' => request('search')])) }}"
                       class="px-2 py-1 rounded-lg hover:bg-slate-800 hover:text-slate-200 transition {{ $loop->last ? 'text-indigo-400 font-semibold' : '' }}">
                        {{ $crumb->name }}
                    </a>
                @endforeach
            </nav>

            <!-- Search & Actions Bar -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <form method="GET" action="{{ route('storage.index') }}" class="flex items-center space-x-2 w-full md:w-96">
                    @if ($currentFolder)
                        <input type="hidden" name="folder" value="{{ $currentFolder->id }}">
                    @endif
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama file/folder..." class="w-full bg-slate-950/80 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-lg shadow-indigo-600/10">Cari</button>
                </form>

                <div class="text-xs text-slate-400">
                    Total File: <strong class="text-slate-200">{{ $files->total() }}</strong>
                    @if ($folders->count())
                        &middot; Total Folder: <strong class="text-slate-200">{{ $folders->count() }}</strong>
                    @endif
                </div>
            </div>

            <!-- Folder Grid -->
            @if ($folders->count())
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach ($folders as $folder)
                        <div class="group relative flex items-center gap-2 bg-slate-950/70 border border-slate-800 hover:border-indigo-500/60 rounded-xl px-3 py-3 transition">
                            <a href="{{ route('storage.index', array_filter(['folder' => $folder->id])) }}" class="flex items-center gap-2 min-w-0 flex-1">
                                <svg class="w-6 h-6 text-amber-400 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"></path>
                                </svg>
                                <span class="truncate text-sm text-slate-200" title="{{ $folder->name }}">{{ $folder->name }}</span>
                            </a>
                            <div class="flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition flex-shrink-0">
                                <button type="button" class="move-folder-btn text-slate-500 hover:text-indigo-400 transition"
                                        data-move-id="{{ $folder->id }}"
                                        data-move-name="{{ $folder->name }}"
                                        data-move-parent-id="{{ $folder->parent_id }}"
                                        title="Pindahkan folder">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14"></path>
                                    </svg>
                                </button>
                                @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'admin')
                                    <form method="POST" action="{{ route('storage.folders.destroy', $folder->id) }}" onsubmit="return confirm('Hapus folder \'{{ $folder->name }}\' beserta seluruh isinya secara permanen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-slate-500 hover:text-rose-400 transition" title="Hapus folder">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Table File -->
            <div id="file-table-wrapper" class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-950/80 text-[11px] tracking-wider text-slate-500 uppercase border-b border-slate-800/80">
                        <tr>
                            <th class="px-4 py-3.5">Nama File</th>
                            <th class="px-4 py-3.5">Uploader</th>
                            <th class="px-4 py-3.5">Tipe / MIME</th>
                            <th class="px-4 py-3.5">Ukuran</th>
                            <th class="px-4 py-3.5">Node</th>
                            <th class="px-4 py-3.5">Tanggal</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse ($files as $file)
                            <tr class="hover:bg-slate-800/50 transition-colors">
                                <td class="px-4 py-3 font-medium text-slate-200 flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="truncate max-w-xs" title="{{ $file->original_name }}">{{ $file->original_name }}</span>
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-400">
                                    {{ $file->user->username ?? 'Unknown' }}
                                </td>
                                <td class="px-4 py-3 text-xs font-mono text-slate-400">
                                    {{ $file->mime_type }}
                                </td>
                                <td class="px-4 py-3 text-xs font-mono">
                                    {{ number_format($file->file_size / (1024 * 1024), 2) }} MB
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <span class="bg-slate-800/80 text-slate-300 px-2.5 py-1 rounded-lg border border-slate-700/80 font-mono text-[11px]">{{ $file->storage_node }}</span>
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-400">
                                    {{ $file->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="px-4 py-3.5 text-right space-x-2">
                                    <a href="{{ route('storage.download', $file->id) }}" class="inline-flex items-center rounded-lg bg-indigo-500/10 px-3 py-1.5 text-xs font-semibold text-indigo-400 hover:bg-indigo-500/15 hover:text-indigo-300 transition">Download</a>
                                    <button type="button" class="move-file-btn inline-flex items-center rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-300 hover:bg-slate-700 hover:text-slate-200 transition"
                                            data-move-id="{{ $file->id }}"
                                            data-move-name="{{ $file->original_name }}">Pindahkan</button>
                                    @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'admin')
                                        <form method="POST" action="{{ route('storage.destroy', $file->id) }}" class="inline" onsubmit="return confirm('Hapus file ini permanently?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center rounded-lg bg-rose-500/10 px-3 py-1.5 text-xs font-semibold text-rose-400 hover:bg-rose-500/15 hover:text-rose-300 transition">Hapus</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8 text-slate-500">
                                    @if ($folders->count())
                                        Tidak ada file di folder ini.
                                    @else
                                        Belum ada file yang di-upload di storage ini.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pt-4">
                {{ $files->links() }}
            </div>

        </div>

    </div>

    <!-- Modal: Buat Folder Baru -->
    <div id="new-folder-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm px-4">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 w-full max-w-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-100">Buat Folder Baru</h3>
            <p class="text-xs text-slate-500">Folder akan dibuat di dalam: <strong class="text-slate-300">{{ $currentFolder->name ?? 'Root' }}</strong></p>
            <form id="new-folder-form" method="POST" action="{{ route('storage.folders.store') }}">
                @csrf
                @if ($currentFolder)
                    <input type="hidden" name="parent_id" value="{{ $currentFolder->id }}">
                @endif
                <input type="text" name="name" required maxlength="255" placeholder="Nama folder"
                       class="w-full bg-slate-950/80 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                <div class="flex items-center justify-end gap-2 pt-4">
                    <button type="button" id="cancel-new-folder-btn" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-300 bg-slate-800 hover:bg-slate-700 transition">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 transition">Buat Folder</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Pindahkan File / Folder -->
    <div id="move-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm px-4">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 w-full max-w-md space-y-4">
            <div>
                <h3 class="text-sm font-bold text-slate-100">Pindahkan <span id="move-modal-item-label" class="text-indigo-400"></span></h3>
                <p class="text-xs text-slate-500 mt-1">Pilih folder tujuan yang sudah ada, atau buat folder baru di bawah.</p>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Folder Tujuan</label>
                <select id="move-target-select" class="w-full bg-slate-950/80 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                    <option value="">Root</option>
                </select>
            </div>

            <div class="space-y-2 border-t border-slate-800 pt-4">
                <label class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Atau Buat Folder Baru</label>
                <div class="flex gap-2">
                    <input type="text" id="move-new-folder-name" maxlength="255" placeholder="Nama folder baru"
                           class="flex-1 bg-slate-950/80 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                    <button type="button" id="move-create-folder-btn" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-200 bg-slate-800 hover:bg-slate-700 transition whitespace-nowrap">+ Buat</button>
                </div>
                <p class="text-[11px] text-slate-500">Folder baru dibuat di dalam folder yang sedang terpilih di atas, lalu otomatis jadi tujuan.</p>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" id="cancel-move-btn" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-300 bg-slate-800 hover:bg-slate-700 transition">Batal</button>
                <button type="button" id="confirm-move-btn" class="px-5 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 transition">Pindahkan</button>
            </div>
        </div>
    </div>

    <!-- JavaScript Chunked Upload Handler -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dropZone = document.getElementById('drop-zone');
            const fileInput = document.getElementById('file-input');
            const folderInput = document.getElementById('folder-input');
            const uploadFolderBtn = document.getElementById('upload-folder-btn');
            const selectedFilesWrapper = document.getElementById('selected-files-wrapper');
            const selectedFilesList = document.getElementById('selected-files-list');
            const cancelSelectionBtn = document.getElementById('cancel-selection-btn');
            const submitUploadBtn = document.getElementById('submit-upload-btn');
            const progressWrapper = document.getElementById('progress-wrapper');
            const progressBar = document.getElementById('progress-bar');
            const uploadFilename = document.getElementById('upload-filename');
            const uploadPercentage = document.getElementById('upload-percentage');
            const uploadStatus = document.getElementById('upload-status');
            const toastContainer = document.getElementById('toast-container');

            const newFolderBtn = document.getElementById('new-folder-btn');
            const newFolderModal = document.getElementById('new-folder-modal');
            const cancelNewFolderBtn = document.getElementById('cancel-new-folder-btn');

            const CHUNK_SIZE = 2 * 1024 * 1024; // 2MB per Chunk
            const CURRENT_FOLDER_ID = document.getElementById('current-folder-meta').dataset.folderId || null;
            const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Menyimpan file yang sudah dipilih tapi belum di-upload.
            // Setiap entri: { file, relativePath, status: 'pending' | 'uploading' | 'success' | 'failed', message }
            let pendingFiles = [];

            // Cache resolusi folder path -> folder_id, supaya 1 folder cuma di-resolve sekali
            // walaupun ada banyak file di dalamnya.
            const folderPathCache = new Map();

            /* Tampilkan flash message dari server (mis. setelah buat/hapus folder) sebagai toast */
            const flashSuccess = document.getElementById('flash-success');
            if (flashSuccess) {
                showToast(flashSuccess.textContent, 'success');
            }

            /* ---------------------------------------------------------
             * Toast Notification (in-page, tidak akan diblokir seperti
             * alert()/confirm() saat berjalan di dalam iframe/preview)
             * --------------------------------------------------------- */
            function showToast(message, type = 'info', duration = 4000) {
                const palette = {
                    success: { bg: 'bg-emerald-950/90', border: 'border-emerald-700', text: 'text-emerald-300', icon: 'M5 13l4 4L19 7' },
                    error:   { bg: 'bg-rose-950/90',    border: 'border-rose-700',    text: 'text-rose-300',    icon: 'M6 18L18 6M6 6l12 12' },
                    info:    { bg: 'bg-indigo-950/90',  border: 'border-indigo-700',  text: 'text-indigo-300',  icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
                };
                const c = palette[type] || palette.info;

                const toast = document.createElement('div');
                toast.className = `flex items-start gap-2.5 ${c.bg} border ${c.border} ${c.text} text-sm rounded-xl px-4 py-3 shadow-xl shadow-black/30 backdrop-blur-sm animate-[fadeIn_0.2s_ease-out]`;
                toast.innerHTML = `
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${c.icon}"></path>
                    </svg>
                    <span class="flex-1 leading-snug"></span>
                    <button type="button" class="flex-shrink-0 opacity-60 hover:opacity-100 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                `;
                toast.querySelector('span').textContent = message;

                const remove = () => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateX(1rem)';
                    setTimeout(() => toast.remove(), 200);
                };
                toast.querySelector('button').addEventListener('click', remove);

                toastContainer.appendChild(toast);
                if (duration > 0) setTimeout(remove, duration);

                return toast;
            }

            /* --------------------- Modal Folder Baru --------------------- */
            newFolderBtn.addEventListener('click', () => newFolderModal.classList.remove('hidden'));
            cancelNewFolderBtn.addEventListener('click', () => newFolderModal.classList.add('hidden'));
            newFolderModal.addEventListener('click', (e) => {
                if (e.target === newFolderModal) newFolderModal.classList.add('hidden');
            });

            /* --------------------- Pilih File Biasa --------------------- */
            dropZone.addEventListener('click', () => fileInput.click());

            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.add('border-indigo-500', 'bg-slate-800/50');
            });

            dropZone.addEventListener('dragleave', () => {
                dropZone.classList.remove('border-indigo-500', 'bg-slate-800/50');
            });

            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-indigo-500', 'bg-slate-800/50');
                if (e.dataTransfer.files.length > 0) {
                    addPendingFiles(e.dataTransfer.files);
                }
            });

            fileInput.addEventListener('change', () => {
                if (fileInput.files.length > 0) {
                    addPendingFiles(fileInput.files);
                }
                fileInput.value = ''; // reset supaya bisa pilih file yang sama lagi
            });

            /* --------------------- Upload Folder (webkitdirectory) --------------------- */
            uploadFolderBtn.addEventListener('click', () => folderInput.click());

            folderInput.addEventListener('change', () => {
                if (folderInput.files.length > 0) {
                    // browser mengisi file.webkitRelativePath, mis. "MyFolder/sub/doc.pdf"
                    addPendingFiles(folderInput.files, true);
                }
                folderInput.value = '';
            });

            cancelSelectionBtn.addEventListener('click', () => {
                clearPendingFiles();
            });

            submitUploadBtn.addEventListener('click', async () => {
                const filesToUpload = pendingFiles.filter((entry) => entry.status !== 'success');
                if (filesToUpload.length === 0) return;

                submitUploadBtn.disabled = true;
                cancelSelectionBtn.disabled = true;
                submitUploadBtn.textContent = 'Mengunggah...';

                let successCount = 0;
                let failedCount = 0;

                for (const entry of filesToUpload) {
                    entry.status = 'uploading';
                    renderSelectedFiles();

                    // Kalau file berasal dari upload folder, pastikan struktur foldernya
                    // sudah ada di server dulu, baru upload ke folder_id yang sesuai.
                    let targetFolderId = CURRENT_FOLDER_ID;
                    if (entry.relativePath) {
                        const dirPath = entry.relativePath.split('/').slice(0, -1).join('/');
                        if (dirPath) {
                            try {
                                targetFolderId = await resolveFolderPath(dirPath, CURRENT_FOLDER_ID);
                            } catch (err) {
                                entry.status = 'failed';
                                entry.message = 'Gagal membuat struktur folder di server.';
                                failedCount++;
                                showToast(`Gagal memproses folder untuk "${entry.file.name}".`, 'error', 7000);
                                renderSelectedFiles();
                                continue;
                            }
                        }
                    }

                    const result = await uploadFileInChunks(entry.file, targetFolderId);

                    if (result.ok) {
                        entry.status = 'success';
                        successCount++;
                        showToast(`Berhasil mengunggah "${entry.file.name}".`, 'success');
                    } else {
                        entry.status = 'failed';
                        entry.message = result.message;
                        failedCount++;
                        showToast(`Gagal mengunggah "${entry.file.name}": ${result.message}`, 'error', 7000);
                    }
                    renderSelectedFiles();
                }

                progressWrapper.classList.add('hidden');
                submitUploadBtn.disabled = false;
                cancelSelectionBtn.disabled = false;
                submitUploadBtn.textContent = 'Upload File';

                if (successCount > 0 && failedCount === 0) {
                    showToast(`Semua file (${successCount}) berhasil diunggah. Memperbarui daftar file...`, 'success');
                    setTimeout(() => window.location.reload(), 1500);
                } else if (successCount > 0 && failedCount > 0) {
                    showToast(`${successCount} file berhasil, ${failedCount} file gagal diunggah. Periksa daftar di atas.`, 'info', 6000);
                    setTimeout(() => window.location.reload(), 2000);
                } else {
                    showToast('Semua file gagal diunggah. Silakan periksa koneksi atau coba lagi.', 'error', 7000);
                }
            });

            function formatBytes(bytes) {
                if (bytes === 0) return '0 B';
                const units = ['B', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(1024));
                return (bytes / Math.pow(1024, i)).toFixed(2) + ' ' + units[i];
            }

            function addPendingFiles(fileList, fromFolder = false) {
                for (let i = 0; i < fileList.length; i++) {
                    const file = fileList[i];
                    pendingFiles.push({
                        file,
                        relativePath: fromFolder ? (file.webkitRelativePath || null) : null,
                        status: 'pending',
                        message: null,
                    });
                }
                renderSelectedFiles();
            }

            function removePendingFile(index) {
                pendingFiles.splice(index, 1);
                renderSelectedFiles();
            }

            function clearPendingFiles() {
                pendingFiles = [];
                renderSelectedFiles();
            }

            function statusBadge(entry) {
                switch (entry.status) {
                    case 'uploading':
                        return '<span class="flex items-center gap-1 text-[11px] text-indigo-400"><svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>Mengunggah</span>';
                    case 'success':
                        return '<span class="flex items-center gap-1 text-[11px] text-emerald-400"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Berhasil</span>';
                    case 'failed':
                        return `<span class="flex items-center gap-1 text-[11px] text-rose-400" title="${entry.message ?? ''}"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>Gagal</span>`;
                    default:
                        return '<span class="text-[11px] text-slate-500">Menunggu</span>';
                }
            }

            function renderSelectedFiles() {
                selectedFilesList.innerHTML = '';

                if (pendingFiles.length === 0) {
                    selectedFilesWrapper.classList.add('hidden');
                    return;
                }

                selectedFilesWrapper.classList.remove('hidden');

                pendingFiles.forEach((entry, index) => {
                    const li = document.createElement('li');
                    li.className = 'flex items-center justify-between bg-slate-950/80 border border-slate-800 rounded-xl px-4 py-2.5 text-sm';
                    const displayName = entry.relativePath || entry.file.name;
                    li.innerHTML = `
                        <div class="flex items-center gap-2 min-w-0">
                            <svg class="w-4 h-4 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            <span class="truncate text-slate-200" title="${displayName}">${displayName}</span>
                            <span class="text-xs text-slate-500 flex-shrink-0">(${formatBytes(entry.file.size)})</span>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0 ml-3">
                            ${statusBadge(entry)}
                            <button type="button" data-index="${index}" class="remove-file-btn text-slate-500 hover:text-rose-400 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    `;
                    selectedFilesList.appendChild(li);
                });

                selectedFilesList.querySelectorAll('.remove-file-btn').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        removePendingFile(parseInt(btn.dataset.index, 10));
                    });
                });
            }

            /**
             * Pastikan rangkaian folder untuk sebuah path relatif ("A/B/C") ada di
             * server (dibuat kalau belum ada), lalu kembalikan id folder paling
             * dalam (leaf). Hasilnya di-cache supaya tidak resolve berulang-ulang
             * untuk file lain yang berada di folder yang sama.
             */
            async function resolveFolderPath(dirPath, rootParentId) {
                const cacheKey = `${rootParentId || 'root'}::${dirPath}`;
                if (folderPathCache.has(cacheKey)) {
                    return folderPathCache.get(cacheKey);
                }

                const body = new URLSearchParams();
                body.append('path', dirPath);
                if (rootParentId) body.append('parent_id', rootParentId);

                const response = await fetch("{{ route('storage.folders.resolve') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body,
                });

                if (!response.ok) {
                    throw new Error('Gagal resolve folder path');
                }

                const data = await response.json();
                folderPathCache.set(cacheKey, data.folder_id);
                return data.folder_id;
            }

            /**
             * Upload satu file secara chunk demi chunk.
             * Mengembalikan { ok: boolean, message: string }
             */
            async function uploadFileInChunks(file, folderId) {
                const totalChunks = Math.ceil(file.size / CHUNK_SIZE);
                const uploadId = 'upload_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);

                progressWrapper.classList.remove('hidden');
                uploadFilename.textContent = file.name;
                progressBar.style.width = '0%';
                uploadPercentage.textContent = '0%';

                for (let chunkIndex = 0; chunkIndex < totalChunks; chunkIndex++) {
                    const start = chunkIndex * CHUNK_SIZE;
                    const end = Math.min(file.size, start + CHUNK_SIZE);
                    const chunk = file.slice(start, end);

                    const formData = new FormData();
                    formData.append('file', chunk);
                    formData.append('upload_id', uploadId);
                    formData.append('chunk_index', chunkIndex);
                    formData.append('total_chunks', totalChunks);
                    formData.append('filename', file.name);
                    if (folderId) formData.append('folder_id', folderId);

                    let response;
                    try {
                        response = await fetch("{{ route('storage.upload') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': CSRF_TOKEN,
                                'Accept': 'application/json'
                            },
                            body: formData
                        });
                    } catch (networkError) {
                        return { ok: false, message: 'Koneksi terputus saat mengirim potongan file.' };
                    }

                    let data = null;
                    try {
                        data = await response.json();
                    } catch (parseError) {
                        // Response bukan JSON valid (misal halaman error HTML dari server)
                    }

                    if (!response.ok) {
                        const serverMessage = data?.message || `Server merespons status ${response.status}.`;
                        return { ok: false, message: serverMessage };
                    }

                    const percent = Math.round(((chunkIndex + 1) / totalChunks) * 100);
                    progressBar.style.width = percent + '%';
                    uploadPercentage.textContent = percent + '%';
                    uploadStatus.textContent = `Mengunggah potongan ${chunkIndex + 1} dari ${totalChunks}...`;

                    // Ini chunk terakhir tapi server belum bilang 'completed' -> anggap gagal
                    if (chunkIndex === totalChunks - 1 && data?.status !== 'completed') {
                        return { ok: false, message: 'Server tidak mengonfirmasi upload selesai (kemungkinan mismatch jumlah chunk).' };
                    }
                }

                uploadStatus.textContent = 'Upload selesai!';
                return { ok: true, message: 'Upload selesai.' };
            }

            /* =========================================================
             * FITUR PINDAHKAN FILE / FOLDER
             * ========================================================= */
            const moveModal = document.getElementById('move-modal');
            const moveModalItemLabel = document.getElementById('move-modal-item-label');
            const moveTargetSelect = document.getElementById('move-target-select');
            const moveNewFolderName = document.getElementById('move-new-folder-name');
            const moveCreateFolderBtn = document.getElementById('move-create-folder-btn');
            const cancelMoveBtn = document.getElementById('cancel-move-btn');
            const confirmMoveBtn = document.getElementById('confirm-move-btn');

            let folderTreeCache = null; // array flat { id, parent_id, name }
            let moveContext = null;     // { type: 'file' | 'folder', id, name }

            async function loadFolderTree() {
                if (folderTreeCache) return folderTreeCache;
                const response = await fetch("{{ route('storage.folders.tree') }}", {
                    headers: { 'Accept': 'application/json' },
                });
                const data = await response.json();
                folderTreeCache = data.folders;
                return folderTreeCache;
            }

            function invalidateFolderTreeCache() {
                folderTreeCache = null;
            }

            function buildChildrenMap(folders) {
                const map = {};
                folders.forEach((f) => {
                    const key = f.parent_id || 'root';
                    if (!map[key]) map[key] = [];
                    map[key].push(f);
                });
                Object.values(map).forEach((list) => list.sort((a, b) => a.name.localeCompare(b.name)));
                return map;
            }

            function getDescendantIds(folders, rootId) {
                const map = buildChildrenMap(folders);
                const result = new Set();
                const walk = (id) => {
                    (map[id] || []).forEach((child) => {
                        result.add(child.id);
                        walk(child.id);
                    });
                };
                walk(rootId);
                return result;
            }

            function buildFolderOptionsHtml(folders, excludeIds) {
                const map = buildChildrenMap(folders);
                const rows = [];
                const walk = (id, depth) => {
                    (map[id] || []).forEach((folder) => {
                        if (!excludeIds.has(folder.id)) {
                            const prefix = depth > 0 ? '　'.repeat(depth) + '↳ ' : '';
                            rows.push(`<option value="${folder.id}">${prefix}${escapeHtml(folder.name)}</option>`);
                        }
                        walk(folder.id, depth + 1);
                    });
                };
                walk('root', 0);
                return rows.join('');
            }

            function escapeHtml(str) {
                const div = document.createElement('div');
                div.textContent = str;
                return div.innerHTML;
            }

            async function refreshMoveTargetOptions(selectValueAfter = null) {
                const folders = await loadFolderTree();
                let excludeIds = new Set();
                if (moveContext?.type === 'folder') {
                    excludeIds = getDescendantIds(folders, moveContext.id);
                    excludeIds.add(moveContext.id);
                }
                moveTargetSelect.innerHTML = '<option value="">Root</option>' + buildFolderOptionsHtml(folders, excludeIds);
                if (selectValueAfter) moveTargetSelect.value = selectValueAfter;
            }

            async function openMoveModal(type, id, name, preselectFolderId) {
                moveContext = { type, id, name };
                moveModalItemLabel.textContent = `"${name}"`;
                moveNewFolderName.value = '';

                await refreshMoveTargetOptions(preselectFolderId || '');

                moveModal.classList.remove('hidden');
            }

            function closeMoveModal() {
                moveModal.classList.add('hidden');
                moveContext = null;
            }

            cancelMoveBtn.addEventListener('click', closeMoveModal);
            moveModal.addEventListener('click', (e) => {
                if (e.target === moveModal) closeMoveModal();
            });

            moveCreateFolderBtn.addEventListener('click', async () => {
                const name = moveNewFolderName.value.trim();
                if (!name) {
                    showToast('Isi dulu nama folder barunya.', 'info');
                    return;
                }

                moveCreateFolderBtn.disabled = true;
                moveCreateFolderBtn.textContent = '...';

                try {
                    const body = new URLSearchParams();
                    body.append('path', name);
                    if (moveTargetSelect.value) body.append('parent_id', moveTargetSelect.value);

                    const response = await fetch("{{ route('storage.folders.resolve') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': CSRF_TOKEN,
                            'Accept': 'application/json',
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body,
                    });

                    if (!response.ok) throw new Error('Gagal membuat folder baru.');
                    const data = await response.json();

                    invalidateFolderTreeCache();
                    await refreshMoveTargetOptions(data.folder_id);

                    moveNewFolderName.value = '';
                    showToast(`Folder "${name}" siap dipakai sebagai tujuan.`, 'success');
                } catch (err) {
                    showToast('Gagal membuat folder baru.', 'error');
                } finally {
                    moveCreateFolderBtn.disabled = false;
                    moveCreateFolderBtn.textContent = '+ Buat';
                }
            });

            confirmMoveBtn.addEventListener('click', async () => {
                if (!moveContext) return;

                confirmMoveBtn.disabled = true;
                confirmMoveBtn.textContent = 'Memindahkan...';

                const targetFolderId = moveTargetSelect.value || '';
                const isFile = moveContext.type === 'file';
                const url = isFile
                    ? `{{ url('/storage') }}/${moveContext.id}/move`
                    : `{{ url('/storage/folders') }}/${moveContext.id}/move`;

                const body = new URLSearchParams();
                body.append(isFile ? 'folder_id' : 'parent_id', targetFolderId);

                try {
                    const response = await fetch(url, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': CSRF_TOKEN,
                            'Accept': 'application/json',
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body,
                    });

                    const data = await response.json().catch(() => null);

                    if (!response.ok) {
                        throw new Error(data?.message || 'Gagal memindahkan.');
                    }

                    showToast(data.message || 'Berhasil dipindahkan.', 'success');
                    closeMoveModal();
                    setTimeout(() => window.location.reload(), 900);
                } catch (err) {
                    showToast(err.message, 'error', 7000);
                } finally {
                    confirmMoveBtn.disabled = false;
                    confirmMoveBtn.textContent = 'Pindahkan';
                }
            });

            document.querySelectorAll('.move-file-btn').forEach((btn) => {
                btn.addEventListener('click', () => {
                    openMoveModal('file', btn.dataset.moveId, btn.dataset.moveName, CURRENT_FOLDER_ID);
                });
            });

            document.querySelectorAll('.move-folder-btn').forEach((btn) => {
                btn.addEventListener('click', () => {
                    openMoveModal('folder', btn.dataset.moveId, btn.dataset.moveName, btn.dataset.moveParentId || '');
                });
            });
        });
    </script>
</x-app-layout>
