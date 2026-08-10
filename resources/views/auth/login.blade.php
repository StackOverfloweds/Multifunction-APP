<x-guest-layout>

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold tracking-tight text-white">
            Welcome Back
        </h1>

        <p class="mt-2 text-sm text-slate-400">
            Sign in to continue to your account.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status
        class="mb-5"
        :status="session('status')"
    />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Username -->
        <div>
            <label
                for="username"
                class="mb-2 block text-sm font-medium text-slate-300"
            >
                Username
            </label>

            <div class="relative">

                <!-- Icon -->
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                    <svg
                        class="h-5 w-5 text-slate-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0z"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M4 21a8 8 0 0116 0"
                        />
                    </svg>
                </div>

                <input
                    id="username"
                    name="username"
                    type="text"
                    value="{{ old('username') }}"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="Enter your username"
                    class="block w-full rounded-xl border border-slate-700/80
                           bg-slate-900/70 py-3.5 pl-11 pr-4
                           text-sm text-white placeholder-slate-500
                           outline-none transition
                           focus:border-indigo-500
                           focus:ring-4 focus:ring-indigo-500/10"
                />

            </div>

            <x-input-error
                :messages="$errors->get('username')"
                class="mt-2"
            />
        </div>

        <!-- Password -->
        <div>
            <div class="mb-2 flex items-center justify-between">

                <label
                    for="password"
                    class="block text-sm font-medium text-slate-300"
                >
                    Password
                </label>

                @if (Route::has('password.request'))
                    <a
                        href="{{ route('password.request') }}"
                        class="text-xs font-medium text-indigo-400
                               transition hover:text-indigo-300"
                    >
                        Forgot password?
                    </a>
                @endif

            </div>

            <div class="relative">

                <!-- Lock Icon -->
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                    <svg
                        class="h-5 w-5 text-slate-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M16 11V7a4 4 0 00-8 0v4"
                        />
                        <rect
                            width="14"
                            height="10"
                            x="5"
                            y="11"
                            rx="2"
                            stroke-width="1.8"
                        />
                    </svg>
                </div>

                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="Enter your password"
                    class="block w-full rounded-xl border border-slate-700/80
                           bg-slate-900/70 py-3.5 pl-11 pr-12
                           text-sm text-white placeholder-slate-500
                           outline-none transition
                           focus:border-indigo-500
                           focus:ring-4 focus:ring-indigo-500/10"
                />

                <!-- Show Password -->
                <button
                    type="button"
                    id="togglePassword"
                    class="absolute inset-y-0 right-0 flex items-center pr-4
                           text-slate-500 transition hover:text-slate-300"
                    aria-label="Show password"
                >
                    <svg
                        id="eyeIcon"
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                        />
                        <circle
                            cx="12"
                            cy="12"
                            r="3"
                            stroke-width="1.8"
                        />
                    </svg>
                </button>

            </div>

            <x-input-error
                :messages="$errors->get('password')"
                class="mt-2"
            />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">

            <label class="inline-flex cursor-pointer items-center">

                <input
                    id="remember_me"
                    type="checkbox"
                    name="remember"
                    class="h-4 w-4 rounded border-slate-700
                           bg-slate-900 text-indigo-600
                           focus:ring-2 focus:ring-indigo-500/30"
                >

                <span class="ms-2 text-sm text-slate-400">
                    Remember me
                </span>

            </label>

        </div>

        <!-- Submit -->
        <button
            type="submit"
            class="group flex w-full items-center justify-center gap-2
                   rounded-xl bg-gradient-to-r from-indigo-500 to-violet-600
                   px-4 py-3.5 text-sm font-semibold text-white
                   shadow-lg shadow-indigo-500/20
                   transition duration-200
                   hover:-translate-y-0.5
                   hover:shadow-xl hover:shadow-indigo-500/30
                   focus:outline-none focus:ring-4 focus:ring-indigo-500/20
                   active:translate-y-0"
        >

            <span>
                Sign In
            </span>

            <svg
                class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M5 12h14M13 6l6 6-6 6"
                />
            </svg>

        </button>

    </form>

    <!-- Register -->
    @if (Route::has('register'))
        <div class="mt-7 border-t border-slate-700/50 pt-6 text-center">

            <p class="text-sm text-slate-400">
                Don't have an account?

                <a
                    href="{{ route('register') }}"
                    class="font-semibold text-indigo-400 transition hover:text-indigo-300"
                >
                    Create an account
                </a>
            </p>

        </div>
    @endif

    <!-- Password Toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePassword = document.getElementById('togglePassword');
            const password = document.getElementById('password');

            if (!togglePassword || !password) return;

            togglePassword.addEventListener('click', function () {
                const type = password.getAttribute('type') === 'password'
                    ? 'text'
                    : 'password';

                password.setAttribute('type', type);

                togglePassword.setAttribute(
                    'aria-label',
                    type === 'password'
                        ? 'Show password'
                        : 'Hide password'
                );
            });
        });
    </script>

</x-guest-layout>