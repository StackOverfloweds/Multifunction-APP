<nav x-data="{ open: false }" class="bg-[#1e293b] border-b border-slate-800/80 shadow-lg shadow-black/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <!-- Logo / Brand -->
            <div class="flex items-center">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-white font-bold shadow-lg shadow-indigo-500/20 group-hover:scale-105 transition-transform">
                            A
                        </div>

                        <span class="hidden sm:block text-lg font-bold text-white tracking-tight group-hover:text-indigo-300 transition-colors">
                            {{ config('app.name', 'Application') }}
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-2 sm:-my-px sm:ms-10 sm:flex items-center">

                    <!-- Dashboard -->
                    <x-nav-link
                        :href="route('dashboard')"
                        :active="request()->routeIs('dashboard')"
                        class="group inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800/70 transition-all duration-200"
                    >
                        <svg
                            class="w-4 h-4 text-slate-400 group-hover:text-indigo-400 transition-colors"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4v-6h4v6h4a1 1 0 001-1V10"
                            />
                        </svg>

                        <span>{{ __('Dashboard') }}</span>
                    </x-nav-link>

                    <!-- Link Akses Storage -->
                    <x-nav-link
                        :href="route('storage.index')"
                        :active="request()->routeIs('storage.*')"
                        class="group inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800/70 transition-all duration-200"
                    >
                        <svg
                            class="w-4 h-4 text-slate-400 group-hover:text-indigo-400 transition-colors"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 7a2 2 0 012-2h5l2 2h7a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"
                            />
                        </svg>

                        <span>{{ __('Storage') }}</span>
                    </x-nav-link>

                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">

                    <x-slot name="trigger">
                        <button
                            type="button"
                            class="inline-flex items-center gap-2 px-3 py-2 border border-slate-700/80 text-sm leading-4 font-medium rounded-xl text-slate-300 bg-slate-800/40 hover:bg-slate-800 hover:text-white hover:border-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 transition-all duration-200"
                        >
                            <!-- Avatar -->
                            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-xs font-bold text-white shadow-lg shadow-indigo-500/20">
                                {{ strtoupper(substr(Auth::user()->username, 0, 1)) }}
                            </div>

                            <!-- Username -->
                            <div class="max-w-[120px] truncate text-slate-300">
                                {{ Auth::user()->username }}
                            </div>

                            <!-- Chevron -->
                            <div class="ms-1">
                                <svg
                                    class="fill-current h-4 w-4 text-slate-500"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="bg-[#1e293b] border border-slate-700/80 rounded-xl shadow-2xl shadow-black/30 overflow-hidden p-1">

                            <!-- Profile -->
                            <a
                                href="{{ route('profile.edit') }}"
                                class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-300 hover:text-white hover:bg-slate-800 transition-all duration-200"
                            >
                                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center">
                                    <svg
                                        class="w-4 h-4 text-indigo-400 group-hover:text-indigo-300"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                        />
                                    </svg>
                                </div>

                                <div>
                                    <div class="font-medium text-slate-200">
                                        {{ __('Profile') }}
                                    </div>

                                    <div class="text-[11px] text-slate-500">
                                        Kelola profil akun
                                    </div>
                                </div>
                            </a>

                            <div class="my-1 border-t border-slate-700/70"></div>

                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <button
                                    type="submit"
                                    class="group w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 transition-all duration-200"
                                >
                                    <div class="w-8 h-8 rounded-lg bg-rose-500/10 border border-rose-500/20 flex items-center justify-center">
                                        <svg
                                            class="w-4 h-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                            />
                                        </svg>
                                    </div>

                                    <div class="text-left">
                                        <div class="font-medium">
                                            {{ __('Log Out') }}
                                        </div>

                                        <div class="text-[11px] text-rose-400/60">
                                            Keluar dari aplikasi
                                        </div>
                                    </div>
                                </button>
                            </form>

                        </div>
                    </x-slot>

                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button
                    @click="open = ! open"
                    class="inline-flex items-center justify-center p-2.5 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 transition-all duration-200"
                >
                    <svg
                        class="h-6 w-6"
                        stroke="currentColor"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <path
                            :class="{'hidden': open, 'inline-flex': ! open}"
                            class="inline-flex"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />

                        <path
                            :class="{'hidden': ! open, 'inline-flex': open}"
                            class="hidden"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div
        :class="{'block': open, 'hidden': ! open}"
        class="hidden sm:hidden bg-[#1e293b] border-t border-slate-800/80"
    >
        <div class="pt-2 pb-3 space-y-1 px-3">

            <x-responsive-nav-link
                :href="route('dashboard')"
                :active="request()->routeIs('dashboard')"
            >
                <div class="flex items-center gap-2">
                    <svg
                        class="w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4v-6h4v6h4a1 1 0 001-1V10"
                        />
                    </svg>

                    <span>{{ __('Dashboard') }}</span>
                </div>
            </x-responsive-nav-link>

            <x-responsive-nav-link
                :href="route('storage.index')"
                :active="request()->routeIs('storage.*')"
            >
                <div class="flex items-center gap-2">
                    <svg
                        class="w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 7a2 2 0 012-2h5l2 2h7a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"
                        />
                    </svg>

                    <span>{{ __('Storage') }}</span>
                </div>
            </x-responsive-nav-link>

        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-3 border-t border-slate-700/70">

            <div class="px-4">
                <div class="flex items-center gap-3">

                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-sm font-bold text-white shadow-lg shadow-indigo-500/20">
                        {{ strtoupper(substr(Auth::user()->username, 0, 1)) }}
                    </div>

                    <div class="min-w-0">
                        <div class="font-medium text-base text-slate-200 truncate">
                            {{ Auth::user()->username }}
                        </div>

                        <div class="font-medium text-sm text-slate-500 truncate">
                            {{ Auth::user()->email }}
                        </div>
                    </div>

                </div>
            </div>

            <div class="mt-3 space-y-1 px-3">

                <x-responsive-nav-link :href="route('profile.edit')">
                    <div class="flex items-center gap-2">
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                            />
                        </svg>

                        <span>{{ __('Profile') }}</span>
                    </div>
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link
                        :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();"
                    >
                        <div class="flex items-center gap-2 text-rose-400">
                            <svg
                                class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                />
                            </svg>

                            <span>{{ __('Log Out') }}</span>
                        </div>
                    </x-responsive-nav-link>
                </form>

            </div>
        </div>
    </div>
</nav>