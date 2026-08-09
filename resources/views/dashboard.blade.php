<x-app-layout>
    <x-slot name="header">
<<<<<<< Updated upstream
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    Selamat datang kembali, <span class="font-bold">{{ Auth::user()->username }}</span>! {{ __("You're logged in!") }}
                </div>
=======
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-slate-100 tracking-tight flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                {{ __('Main Dashboard') }}
            </h2>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 text-xs font-mono font-semibold rounded-full bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 shadow-sm">
                    ROLE: {{ strtoupper(auth()->user()->role ?? 'USER') }}
                </span>
>>>>>>> Stashed changes
            </div>
        </div>
    </x-slot>

    <div class="relative min-h-[calc(100vh-80px)] py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div class="pointer-events-none absolute inset-x-0 top-0 -z-0 h-72 bg-gradient-to-b from-indigo-500/[0.06] to-transparent"></div>
        
        <!-- Welcome Hero Banner dengan Gradient & Glassmorphism -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900/95 via-indigo-950/70 to-slate-950 p-8 sm:p-10 border border-slate-800/80 shadow-2xl shadow-black/30 backdrop-blur-sm">
            <!-- Background Glow Decor -->
            <div class="absolute -right-16 -top-16 w-80 h-80 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-16 -bottom-16 w-80 h-80 bg-violet-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-2xl space-y-3">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-300 text-xs font-medium">
                    <svg class="w-3.5 h-3.5 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                    </svg>
                    Multifunction Engine Center
                </div>
                <h3 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                    Selamat Datang Kembali, <span class="bg-gradient-to-r from-indigo-400 to-violet-400 bg-clip-text text-transparent capitalize">{{ auth()->user()->username ?? 'User' }}</span> 👋
                </h3>
                <p class="text-slate-300/80 text-sm leading-relaxed max-w-xl">
                    Akses seluruh modul fungsional aplikasi dari satu portal terpusat. Pilih layanan di bawah ini untuk memulai pengoperasian sistem.
                </p>
            </div>
        </div>

        <!-- Section Title -->
        <div class="flex items-center justify-between border-b border-slate-800/80 pb-4">
            <div>
                <h4 class="text-lg font-bold text-slate-100 tracking-tight">Application Modules</h4>
                <p class="text-xs text-slate-400">Pilih modul fungsional yang tersedia untuk hak akses Anda</p>
            </div>
        </div>

        <!-- Module Selection Grid Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">

            <!-- Card 1: Storage Manager (Active) -->
            <a href="{{ route('storage.index') }}" class="group relative bg-slate-900/80 hover:bg-slate-900/95 border border-slate-800/80 hover:border-indigo-500/50 rounded-2xl p-6 shadow-xl shadow-black/20 hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-300 ease-out flex flex-col justify-between overflow-hidden hover:-translate-y-1">
                <!-- Top Accent Highlight -->
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 to-violet-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center group-hover:scale-110 group-hover:bg-indigo-500 group-hover:text-white transition-all duration-300 shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <span class="inline-flex items-center gap-1.5 text-[10px] font-mono font-bold px-2.5 py-1 rounded-md bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>ACTIVE</span>
                    </div>

                    <div>
                        <h5 class="text-lg font-bold text-slate-100 group-hover:text-indigo-400 transition-colors">
                            Storage Manager
                        </h5>
                        <p class="text-sm text-slate-400 mt-2 leading-6">
                            Penyimpanan file berbasis mikroservis (Word, PDF, Gambar, ISO). Mendukung *chunked upload* tanpa batasan ukuran file.
                        </p>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-800/80 flex items-center justify-between group-hover:border-slate-700/80 text-xs font-semibold text-indigo-400">
                    <span>Buka Storage Engine</span>
                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </div>
            </a>

            <!-- Card 2: Server Public Metrics -->
            <a href="{{ route('welcome') }}" target="_blank" class="group relative bg-slate-900/80 hover:bg-slate-900/95 border border-slate-800/80 hover:border-cyan-500/50 rounded-2xl p-6 shadow-xl shadow-black/20 hover:shadow-2xl hover:shadow-cyan-500/10 transition-all duration-300 ease-out flex flex-col justify-between overflow-hidden hover:-translate-y-1">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-cyan-500 to-blue-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center group-hover:scale-110 group-hover:bg-cyan-500 group-hover:text-white transition-all duration-300 shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <span class="inline-flex items-center gap-1.5 text-[10px] font-mono font-bold px-2.5 py-1 rounded-md bg-cyan-500/10 text-cyan-400 border border-cyan-500/20"><span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>LIVE SYSTEM</span>
                    </div>

                    <div>
                        <h5 class="text-lg font-bold text-slate-100 group-hover:text-cyan-400 transition-colors">
                            Server Public Metrics
                        </h5>
                        <p class="text-sm text-slate-400 mt-2 leading-6">
                            Pantau performa hardware server (CPU Load, RAM, Disk Usage, Uptime) secara real-time via grafik Chart.js.
                        </p>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-800/80 flex items-center justify-between group-hover:border-slate-700/80 text-xs font-semibold text-cyan-400">
                    <span>Lihat Public Dashboard</span>
                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                </div>
            </a>

            <!-- Card 3: User Management (Admin Only) -->
            @if(in_array(auth()->user()->role ?? 'user', ['super_admin', 'admin']))
            <div class="group relative bg-slate-900/80 hover:bg-slate-900/95 border border-slate-800/80 hover:border-amber-500/50 rounded-2xl p-6 shadow-xl shadow-black/20 hover:shadow-2xl hover:shadow-amber-500/10 transition-all duration-300 ease-out flex flex-col justify-between overflow-hidden hover:-translate-y-1 cursor-pointer">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-500 to-orange-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center group-hover:scale-110 group-hover:bg-amber-500 group-hover:text-white transition-all duration-300 shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <span class="text-[10px] font-mono font-bold px-2.5 py-1 rounded-md bg-amber-500/10 text-amber-400 border border-amber-500/20">ADMIN ONLY</span>
                    </div>

                    <div>
                        <h5 class="text-lg font-bold text-slate-100 group-hover:text-amber-400 transition-colors">
                            User Management
                        </h5>
                        <p class="text-sm text-slate-400 mt-2 leading-6">
                            Pengelolaan hierarki hak akses pengguna (Super Admin, Admin, & User) serta kontrol otentikasi.
                        </p>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-800/80 flex items-center justify-between group-hover:border-slate-700/80 text-xs font-semibold text-amber-400">
                    <span>Modul Siap Dikembangkan</span>
                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </div>
            </div>
            @endif

        </div>

    </div>
</x-app-layout>