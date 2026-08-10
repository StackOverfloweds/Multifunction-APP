<x-guest-layout>

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold tracking-tight text-white">
            Create Your Account
        </h1>

        <p class="mt-2 text-sm text-slate-400">
            Register your account to get started.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
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
                    placeholder="Choose a username"
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

        <!-- Email -->
        <div>
            <label
                for="email"
                class="mb-2 block text-sm font-medium text-slate-300"
            >
                Email
            </label>

            <div class="relative">

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
                            d="M3 8l9 6 9-6"
                        />
                        <rect
                            width="18"
                            height="14"
                            x="3"
                            y="5"
                            rx="2"
                            stroke-width="1.8"
                        />
                    </svg>
                </div>

                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="username"
                    placeholder="you@example.com"
                    class="block w-full rounded-xl border border-slate-700/80
                           bg-slate-900/70 py-3.5 pl-11 pr-4
                           text-sm text-white placeholder-slate-500
                           outline-none transition
                           focus:border-indigo-500
                           focus:ring-4 focus:ring-indigo-500/10"
                />

            </div>

            <x-input-error
                :messages="$errors->get('email')"
                class="mt-2"
            />
        </div>

        <!-- Password -->
        <div>
            <label
                for="password"
                class="mb-2 block text-sm font-medium text-slate-300"
            >
                Password
            </label>

            <div class="relative">

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
                    autocomplete="new-password"
                    placeholder="Create a strong password"
                    class="block w-full rounded-xl border border-slate-700/80
                           bg-slate-900/70 py-3.5 pl-11 pr-12
                           text-sm text-white placeholder-slate-500
                           outline-none transition
                           focus:border-indigo-500
                           focus:ring-4 focus:ring-indigo-500/10"
                />

                <button
                    type="button"
                    data-toggle-password="password"
                    class="absolute inset-y-0 right-0 flex items-center pr-4
                           text-slate-500 transition hover:text-slate-300"
                    aria-label="Show password"
                >
                    <svg
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

        <!-- Confirm Password -->
        <div>
            <label
                for="password_confirmation"
                class="mb-2 block text-sm font-medium text-slate-300"
            >
                Confirm Password
            </label>

            <div class="relative">

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
                            d="M9 12l2 2 4-4"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M12 3l7 4v5c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V7l7-4z"
                        />
                    </svg>
                </div>

                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="Confirm your password"
                    class="block w-full rounded-xl border border-slate-700/80
                           bg-slate-900/70 py-3.5 pl-11 pr-12
                           text-sm text-white placeholder-slate-500
                           outline-none transition
                           focus:border-indigo-500
                           focus:ring-4 focus:ring-indigo-500/10"
                />

                <button
                    type="button"
                    data-toggle-password="password_confirmation"
                    class="absolute inset-y-0 right-0 flex items-center pr-4
                           text-slate-500 transition hover:text-slate-300"
                    aria-label="Show password"
                >
                    <svg
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
                :messages="$errors->get('password_confirmation')"
                class="mt-2"
            />
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
                Create Account
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

    <!-- Login -->
    <div class="mt-7 border-t border-slate-700/50 pt-6 text-center">

        <p class="text-sm text-slate-400">
            Already have an account?

            <a
                href="{{ route('login') }}"
                class="font-semibold text-indigo-400 transition hover:text-indigo-300"
            >
                Sign in
            </a>
        </p>

    </div>

    <!-- Password Toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            document
                .querySelectorAll('[data-toggle-password]')
                .forEach(function (button) {

                    button.addEventListener('click', function () {

                        const targetId = button.dataset.togglePassword;
                        const input = document.getElementById(targetId);

                        if (!input) return;

                        const isPassword = input.type === 'password';

                        input.type = isPassword ? 'text' : 'password';

                        button.setAttribute(
                            'aria-label',
                            isPassword
                                ? 'Hide password'
                                : 'Show password'
                        );
                    });

                });

        });
    </script>

</x-guest-layout>