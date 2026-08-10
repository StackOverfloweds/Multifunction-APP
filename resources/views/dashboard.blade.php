<x-app-layout>

    <!-- ============================= -->
    <!-- PAGE HEADER -->
    <!-- ============================= -->

    <x-slot name="header">

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <!-- Title -->
            <div class="min-w-0">

                <div class="flex items-center gap-2">

                    <span
                        class="h-2.5 w-2.5 shrink-0 rounded-full
                               bg-emerald-500 shadow-lg shadow-emerald-500/30
                               animate-pulse"
                    ></span>

                    <h2
                        class="truncate text-lg font-bold tracking-tight
                               text-slate-100 sm:text-xl"
                    >
                        {{ __('Main Dashboard') }}
                    </h2>

                </div>

                <p class="mt-1 text-xs text-slate-500 sm:hidden">
                    Multifunction Application Center
                </p>

            </div>

            <!-- Role -->
            <div class="flex shrink-0 items-center">

                <span
                    class="inline-flex items-center gap-1.5 rounded-full
                           border border-indigo-500/20
                           bg-indigo-500/10
                           px-3 py-1.5
                           text-[10px] font-bold font-mono
                           text-indigo-400 sm:text-xs"
                >
                    <svg
                        class="h-3.5 w-3.5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M12 15a3 3 0 100-6 3 3 0 000 6z"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M19.4 15a1.7 1.7 0 00.34 1.88l.06.06-1.8 1.8-.06-.06a1.7 1.7 0 00-1.88-.34 1.7 1.7 0 00-1.03 1.56V20h-2.54v-.1a1.7 1.7 0 00-1.03-1.56 1.7 1.7 0 00-1.88.34l-.06.06-1.8-1.8.06-.06A1.7 1.7 0 008.1 15a1.7 1.7 0 00-1.56-1.03H6v-2.54h.54A1.7 1.7 0 008.1 10.4a1.7 1.7 0 00-.34-1.88L7.7 8.46l1.8-1.8.06.06a1.7 1.7 0 001.88.34A1.7 1.7 0 0012.47 5.5V5h2.54v.5a1.7 1.7 0 001.03 1.56 1.7 1.7 0 001.88-.34l.06-.06 1.8 1.8-.06.06a1.7 1.7 0 00-.34 1.88A1.7 1.7 0 0019.4 11.43H20v2.54h-.6A1.7 1.7 0 0019.4 15z"
                        />
                    </svg>

                    ROLE:
                    {{ strtoupper(auth()->user()->role ?? 'USER') }}
                </span>

            </div>

        </div>

    </x-slot>


    <!-- ============================= -->
    <!-- MAIN CONTENT -->
    <!-- ============================= -->

    <div
        class="relative mx-auto min-h-[calc(100vh-80px)]
               w-full max-w-7xl
               space-y-6 px-4 py-5
               sm:space-y-8 sm:px-6 sm:py-8
               lg:px-8"
    >

        <!-- Background Glow -->
        <div
            class="pointer-events-none absolute inset-x-0 top-0
                   -z-0 h-48
                   bg-gradient-to-b from-indigo-500/[0.07]
                   to-transparent
                   sm:h-72"
        ></div>


        <!-- ============================= -->
        <!-- WELCOME HERO -->
        <!-- ============================= -->

        <section
            class="relative overflow-hidden rounded-2xl
                   border border-slate-800/80
                   bg-gradient-to-br
                   from-slate-900/95
                   via-indigo-950/70
                   to-slate-950
                   p-5 shadow-2xl shadow-black/30
                   backdrop-blur-sm
                   sm:rounded-3xl sm:p-8
                   lg:p-10"
        >

            <!-- Decorative Glow -->
            <div
                class="pointer-events-none absolute
                       -right-24 -top-24
                       h-56 w-56 rounded-full
                       bg-indigo-500/20 blur-3xl
                       sm:h-80 sm:w-80"
            ></div>

            <div
                class="pointer-events-none absolute
                       -bottom-24 -left-24
                       h-56 w-56 rounded-full
                       bg-violet-500/10 blur-3xl
                       sm:h-80 sm:w-80"
            ></div>


            <div class="relative z-10 max-w-3xl space-y-3 sm:space-y-4">

                <!-- Badge -->
                <div
                    class="inline-flex max-w-full items-center gap-2
                           rounded-full border border-indigo-500/20
                           bg-indigo-500/10
                           px-3 py-1.5
                           text-[10px] font-medium text-indigo-300
                           sm:text-xs"
                >

                    <svg
                        class="h-3.5 w-3.5 shrink-0 text-indigo-400"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"
                        />
                    </svg>

                    <span class="truncate">
                        Multifunction Engine Center
                    </span>

                </div>


                <!-- Heading -->
                <h3
                    class="text-2xl font-extrabold leading-tight
                           tracking-tight text-white
                           sm:text-3xl
                           lg:text-4xl"
                >
                    Selamat Datang Kembali,

                    <span
                        class="bg-gradient-to-r
                               from-indigo-400 to-violet-400
                               bg-clip-text
                               text-transparent
                               capitalize"
                    >
                        {{ auth()->user()->username ?? 'User' }}
                    </span>

                    <span class="whitespace-nowrap">👋</span>
                </h3>


                <!-- Description -->
                <p
                    class="max-w-2xl text-sm leading-6
                           text-slate-300/80
                           sm:text-base"
                >
                    Akses seluruh modul fungsional aplikasi dari satu
                    portal terpusat. Pilih layanan di bawah ini untuk
                    memulai pengoperasian sistem.
                </p>

            </div>

        </section>


        <!-- ============================= -->
        <!-- MODULE HEADER -->
        <!-- ============================= -->

        <section>

            <div
                class="flex flex-col gap-2
                       border-b border-slate-800/80
                       pb-4
                       sm:flex-row sm:items-end
                       sm:justify-between"
            >

                <div>

                    <h4
                        class="text-base font-bold tracking-tight
                               text-slate-100
                               sm:text-lg"
                    >
                        Application Modules
                    </h4>

                    <p class="mt-1 text-xs text-slate-400 sm:text-sm">
                        Pilih modul fungsional yang tersedia
                        untuk hak akses Anda
                    </p>

                </div>

                <span
                    class="hidden text-[10px] font-mono
                           uppercase tracking-wider text-slate-600
                           sm:block"
                >
                    Multifunction APP
                </span>

            </div>

        </section>


        <!-- ============================= -->
        <!-- MODULE GRID -->
        <!-- ============================= -->

        <div
            class="grid grid-cols-1 gap-4
                   sm:grid-cols-2 sm:gap-5
                   xl:grid-cols-3"
        >

            <!-- ========================= -->
            <!-- STORAGE MANAGER -->
            <!-- ========================= -->

            <a
                href="{{ route('storage.index') }}"
                class="group relative flex min-h-[300px]
                       flex-col justify-between
                       overflow-hidden rounded-2xl
                       border border-slate-800/80
                       bg-slate-900/80
                       p-5
                       shadow-xl shadow-black/20
                       transition-all duration-300
                       hover:-translate-y-1
                       hover:border-indigo-500/50
                       hover:bg-slate-900/95
                       hover:shadow-2xl
                       hover:shadow-indigo-500/10
                       focus:outline-none
                       focus:ring-2
                       focus:ring-indigo-500/50
                       sm:p-6"
            >

                <!-- Top Accent -->
                <div
                    class="absolute left-0 right-0 top-0 h-1
                           bg-gradient-to-r
                           from-indigo-500 to-violet-500
                           opacity-0
                           transition-opacity
                           group-hover:opacity-100"
                ></div>


                <div class="space-y-4">

                    <div class="flex items-center justify-between gap-3">

                        <div
                            class="flex h-11 w-11 shrink-0
                                   items-center justify-center
                                   rounded-xl
                                   border border-indigo-500/20
                                   bg-indigo-500/10
                                   text-indigo-400
                                   shadow-inner
                                   transition-all duration-300
                                   group-hover:scale-110
                                   group-hover:bg-indigo-500
                                   group-hover:text-white
                                   sm:h-12 sm:w-12"
                        >
                            <svg
                                class="h-5 w-5 sm:h-6 sm:w-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"
                                />
                            </svg>
                        </div>

                        <span
                            class="inline-flex shrink-0 items-center
                                   gap-1.5 rounded-md
                                   border border-emerald-500/20
                                   bg-emerald-500/10
                                   px-2 py-1
                                   text-[9px] font-bold font-mono
                                   text-emerald-400
                                   sm:text-[10px]"
                        >
                            <span
                                class="h-1.5 w-1.5 rounded-full
                                       bg-emerald-400 animate-pulse"
                            ></span>
                            ACTIVE
                        </span>

                    </div>


                    <div>

                        <h5
                            class="text-lg font-bold text-slate-100
                                   transition-colors
                                   group-hover:text-indigo-400"
                        >
                            Storage Manager
                        </h5>

                        <p
                            class="mt-2 text-sm leading-6
                                   text-slate-400"
                        >
                            Penyimpanan file berbasis mikroservis
                            (Word, PDF, Gambar, ISO). Mendukung
                            <em>chunked upload</em> tanpa batasan
                            ukuran file.
                        </p>

                    </div>

                </div>


                <div
                    class="mt-6 flex items-center
                           justify-between gap-3
                           border-t border-slate-800/80
                           pt-4 text-xs font-semibold
                           text-indigo-400
                           transition-colors
                           group-hover:border-slate-700/80"
                >

                    <span class="truncate">
                        Buka Storage Engine
                    </span>

                    <svg
                        class="h-4 w-4 shrink-0
                               transition-transform
                               group-hover:translate-x-1"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3"
                        />
                    </svg>

                </div>

            </a>


            <!-- ========================= -->
            <!-- SERVER METRICS -->
            <!-- ========================= -->

            <a
                href="{{ route('welcome') }}"
                target="_blank"
                rel="noopener noreferrer"
                class="group relative flex min-h-[300px]
                       flex-col justify-between
                       overflow-hidden rounded-2xl
                       border border-slate-800/80
                       bg-slate-900/80
                       p-5
                       shadow-xl shadow-black/20
                       transition-all duration-300
                       hover:-translate-y-1
                       hover:border-cyan-500/50
                       hover:bg-slate-900/95
                       hover:shadow-2xl
                       hover:shadow-cyan-500/10
                       focus:outline-none
                       focus:ring-2
                       focus:ring-cyan-500/50
                       sm:p-6"
            >

                <div
                    class="absolute left-0 right-0 top-0 h-1
                           bg-gradient-to-r
                           from-cyan-500 to-blue-500
                           opacity-0
                           transition-opacity
                           group-hover:opacity-100"
                ></div>


                <div class="space-y-4">

                    <div class="flex items-center justify-between gap-3">

                        <div
                            class="flex h-11 w-11 shrink-0
                                   items-center justify-center
                                   rounded-xl
                                   border border-cyan-500/20
                                   bg-cyan-500/10
                                   text-cyan-400
                                   shadow-inner
                                   transition-all duration-300
                                   group-hover:scale-110
                                   group-hover:bg-cyan-500
                                   group-hover:text-white
                                   sm:h-12 sm:w-12"
                        >
                            <svg
                                class="h-5 w-5 sm:h-6 sm:w-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                />
                            </svg>
                        </div>

                        <span
                            class="inline-flex shrink-0 items-center
                                   gap-1.5 rounded-md
                                   border border-cyan-500/20
                                   bg-cyan-500/10
                                   px-2 py-1
                                   text-[9px] font-bold font-mono
                                   text-cyan-400
                                   sm:text-[10px]"
                        >
                            <span
                                class="h-1.5 w-1.5 rounded-full
                                       bg-cyan-400 animate-pulse"
                            ></span>
                            LIVE SYSTEM
                        </span>

                    </div>


                    <div>

                        <h5
                            class="text-lg font-bold text-slate-100
                                   transition-colors
                                   group-hover:text-cyan-400"
                        >
                            Server Public Metrics
                        </h5>

                        <p
                            class="mt-2 text-sm leading-6
                                   text-slate-400"
                        >
                            Pantau performa hardware server
                            (CPU Load, RAM, Disk Usage, Uptime)
                            secara real-time via grafik Chart.js.
                        </p>

                    </div>

                </div>


                <div
                    class="mt-6 flex items-center
                           justify-between gap-3
                           border-t border-slate-800/80
                           pt-4 text-xs font-semibold
                           text-cyan-400
                           transition-colors
                           group-hover:border-slate-700/80"
                >

                    <span class="truncate">
                        Lihat Public Dashboard
                    </span>

                    <svg
                        class="h-4 w-4 shrink-0
                               transition-transform
                               group-hover:translate-x-1"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                        />
                    </svg>

                </div>

            </a>


            <!-- ========================= -->
            <!-- USER MANAGEMENT -->
            <!-- ========================= -->

            @if(in_array(auth()->user()->role ?? 'user', ['super_admin', 'admin']))

                <div
                    class="group relative flex min-h-[300px]
                           flex-col justify-between
                           overflow-hidden rounded-2xl
                           border border-slate-800/80
                           bg-slate-900/80
                           p-5
                           shadow-xl shadow-black/20
                           transition-all duration-300
                           hover:-translate-y-1
                           hover:border-amber-500/50
                           hover:bg-slate-900/95
                           hover:shadow-2xl
                           hover:shadow-amber-500/10
                           sm:p-6"
                >

                    <div
                        class="absolute left-0 right-0 top-0 h-1
                               bg-gradient-to-r
                               from-amber-500 to-orange-500
                               opacity-0
                               transition-opacity
                               group-hover:opacity-100"
                    ></div>


                    <div class="space-y-4">

                        <div class="flex items-center justify-between gap-3">

                            <div
                                class="flex h-11 w-11 shrink-0
                                       items-center justify-center
                                       rounded-xl
                                       border border-amber-500/20
                                       bg-amber-500/10
                                       text-amber-400
                                       shadow-inner
                                       transition-all duration-300
                                       group-hover:scale-110
                                       group-hover:bg-amber-500
                                       group-hover:text-white
                                       sm:h-12 sm:w-12"
                            >
                                <svg
                                    class="h-5 w-5 sm:h-6 sm:w-6"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                    />
                                </svg>
                            </div>

                            <span
                                class="shrink-0 rounded-md
                                       border border-amber-500/20
                                       bg-amber-500/10
                                       px-2 py-1
                                       text-[9px] font-bold font-mono
                                       text-amber-400
                                       sm:text-[10px]"
                            >
                                ADMIN ONLY
                            </span>

                        </div>


                        <div>

                            <h5
                                class="text-lg font-bold
                                       text-slate-100
                                       transition-colors
                                       group-hover:text-amber-400"
                            >
                                User Management
                            </h5>

                            <p
                                class="mt-2 text-sm leading-6
                                       text-slate-400"
                            >
                                Pengelolaan hierarki hak akses pengguna
                                (Super Admin, Admin, & User) serta
                                kontrol otentikasi.
                            </p>

                        </div>

                    </div>


                    <div
                        class="mt-6 flex items-center
                               justify-between gap-3
                               border-t border-slate-800/80
                               pt-4 text-xs font-semibold
                               text-amber-400
                               transition-colors
                               group-hover:border-slate-700/80"
                    >

                        <span class="truncate">
                            Modul Siap Dikembangkan
                        </span>

                        <svg
                            class="h-4 w-4 shrink-0
                                   transition-transform
                                   group-hover:translate-x-1"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3"
                            />
                        </svg>

                    </div>

                </div>

            @endif

        </div>


        <!-- ============================= -->
        <!-- FOOTER INFO -->
        <!-- ============================= -->

        <div
            class="border-t border-slate-800/60
                   pt-5 text-center"
        >

            <p class="text-[10px] text-slate-600 sm:text-xs">
                Multifunction APP
                <span class="mx-1">•</span>
                {{ date('Y') }}
            </p>

        </div>

    </div>

</x-app-layout>
