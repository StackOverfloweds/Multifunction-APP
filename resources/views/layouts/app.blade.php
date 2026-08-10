<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MultiFunction-APP') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link
        href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap"
        rel="stylesheet"
    />

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Figtree', 'sans-serif'],
                    },
                },
            },
        }
    </script>

    <style>
        html {
            scroll-behavior: smooth;
        }

        body {
            overflow-x: hidden;
        }

        /* Mobile scrollbar */
        ::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }

        ::-webkit-scrollbar-track {
            background: #0f172a;
        }

        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 999px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }

        /* Prevent content from becoming wider than viewport */
        .app-content {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
        }
    </style>
</head>

<body
    class="min-h-screen bg-[#1e293b] font-sans antialiased text-slate-100
           selection:bg-indigo-500/30 selection:text-indigo-200"
>

    <div class="min-h-screen w-full bg-[#1e293b]">

        <!-- Navigation -->
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header
                class="sticky top-0 z-30 border-b border-slate-800/80
                       bg-[#1e293b]/95 shadow-lg shadow-black/10
                       backdrop-blur-xl"
            >
                <div
                    class="mx-auto w-full max-w-7xl
                           px-4 py-4
                           sm:px-6 sm:py-5
                           lg:px-8"
                >
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="app-content w-full">
            {{ $slot }}
        </main>

    </div>

</body>
</html>

