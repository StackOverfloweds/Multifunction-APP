<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Multifunction APP') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .auth-bg {
            background:
                radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.15), transparent 30%),
                radial-gradient(circle at 90% 80%, rgba(139, 92, 246, 0.12), transparent 30%),
                #0f172a;
        }

        .auth-card {
            background: rgba(15, 23, 42, 0.78);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(148, 163, 184, 0.12);
            box-shadow:
                0 25px 60px rgba(0, 0, 0, 0.35),
                0 0 0 1px rgba(255, 255, 255, 0.02);
        }

        .auth-grid {
            background-image:
                linear-gradient(rgba(148, 163, 184, 0.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(148, 163, 184, 0.025) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        .auth-glow {
            filter: blur(70px);
        }
    </style>
</head>

<body class="font-sans antialiased text-slate-100">

    <div class="auth-bg auth-grid relative min-h-screen overflow-hidden">

        <!-- Background Glow -->
        <div class="auth-glow absolute -left-32 -top-32 h-96 w-96 rounded-full bg-indigo-600/20"></div>
        <div class="auth-glow absolute -bottom-32 -right-32 h-96 w-96 rounded-full bg-violet-600/20"></div>

        <!-- Main Content -->
        <div class="relative flex min-h-screen items-center justify-center px-4 py-10 sm:px-6">

            <div class="w-full max-w-md">

                <!-- Brand -->
                <div class="mb-8 text-center">

                    <a href="{{ url('/') }}"
                       class="group inline-flex flex-col items-center">

                        <div
                            class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl
                                   bg-gradient-to-br from-indigo-500 to-violet-600
                                   shadow-lg shadow-indigo-500/25
                                   transition duration-300
                                   group-hover:scale-105 group-hover:shadow-indigo-500/40">

                            <svg
                                class="h-8 w-8 text-white"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                                <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                                <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                                <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                            </svg>

                        </div>

                        <span class="text-xl font-bold tracking-tight text-white">
                            Multifunction
                            <span class="text-indigo-400">APP</span>
                        </span>

                    </a>

                    <p class="mt-2 text-sm text-slate-400">
                        Your multifunction application platform
                    </p>

                </div>

                <!-- Authentication Card -->
                <div class="auth-card overflow-hidden rounded-2xl p-6 sm:p-8">

                    {{ $slot }}

                </div>

                <!-- Footer -->
                <div class="mt-6 text-center">

                    <p class="text-xs text-slate-500">
                        &copy; {{ date('Y') }} Multifunction APP.
                        All rights reserved.
                    </p>

                </div>

            </div>

        </div>

    </div>

</body>
</html>