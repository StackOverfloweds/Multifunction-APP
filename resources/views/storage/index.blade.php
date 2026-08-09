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
            
            <!-- Search & Actions Bar -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <form method="GET" action="{{ route('storage.index') }}" class="flex items-center space-x-2 w-full md:w-96">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama file atau ekstensi..." class="w-full bg-slate-950/80 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-lg shadow-indigo-600/10">Cari</button>
                </form>
                
                <div class="text-xs text-slate-400">
                    Total File: <strong class="text-slate-200">{{ $files->total() }}</strong>
                </div>
            </div>

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
                                    Belum ada file yang di-upload di storage ini.
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

    <!-- JavaScript Chunked Upload Handler -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dropZone = document.getElementById('drop-zone');
            const fileInput = document.getElementById('file-input');
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

            const CHUNK_SIZE = 2 * 1024 * 1024; // 2MB per Chunk

            // Menyimpan file yang sudah dipilih tapi belum di-upload.
            // Setiap entri: { file, status: 'pending' | 'uploading' | 'success' | 'failed', message }
            let pendingFiles = [];

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

                    const result = await uploadFileInChunks(entry.file);

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

            function addPendingFiles(fileList) {
                for (let i = 0; i < fileList.length; i++) {
                    pendingFiles.push({ file: fileList[i], status: 'pending', message: null });
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
                    li.innerHTML = `
                        <div class="flex items-center gap-2 min-w-0">
                            <svg class="w-4 h-4 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            <span class="truncate text-slate-200" title="${entry.file.name}">${entry.file.name}</span>
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
             * Upload satu file secara chunk demi chunk.
             * Mengembalikan { ok: boolean, message: string }
             */
            async function uploadFileInChunks(file) {
                const totalChunks = Math.ceil(file.size / CHUNK_SIZE);
                const uploadId = 'upload_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

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

                    let response;
                    try {
                        response = await fetch("{{ route('storage.upload') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
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
        });
    </script>
</x-app-layout>