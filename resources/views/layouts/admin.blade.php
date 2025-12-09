<!DOCTYPE html>
{{-- 
    Layout panelu administracyjnego.
    Używany przez wszystkie widoki związane z zarządzaniem quizami i pytaniami.
    Zawiera nagłówek z nawigacją, obszar na komunikaty i błędy, oraz sekcję główną na treść.
--}}
<html lang="pl">
    <head>
        {{-- Podstawowe meta tagi --}}
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Panel Administratora - QuizApp' }}</title>
        @php
            $hasViteBuild = file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'));
        @endphp

        @if ($hasViteBuild)
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <link rel="preconnect" href="https://fonts.bunny.net">
            <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" rel="stylesheet" />
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.13/dist/tailwind.min.css">
        @endif

        <style>
            :root {
                font-family: 'Instrument Sans', system-ui, sans-serif;
                color-scheme: dark;
            }
            body {
                background: radial-gradient(circle at top, #111827, #0f172a 45%, #0b1121);
                color: #e2e8f0;
            }
            .shell {
                background: rgba(15, 23, 42, 0.7);
                border: 1px solid rgba(148, 163, 184, 0.08);
                border-radius: 28px;
                box-shadow: 0 20px 40px rgba(2, 6, 23, 0.35);
                backdrop-filter: blur(14px);
            }
            .accent-border {
                border: 1px solid rgba(99, 102, 241, 0.25);
            }
        </style>
    </head>
    <body class="min-h-screen px-4 py-8 text-slate-100 antialiased md:px-6">
        <div class="mx-auto flex min-h-screen w-full max-w-7xl flex-col gap-8">
            <header class="shell flex flex-col gap-4 p-6 md:flex-row md:items-center md:justify-between">
                <div>
                    <a href="{{ route('admin.quizzes.index') }}" class="text-2xl font-semibold text-white">Panel Administratora</a>
                    <p class="text-sm text-slate-400">Zarządzanie quizami i pytaniami</p>
                </div>
                <nav class="flex flex-wrap gap-3 text-sm text-slate-300">
                    <a href="{{ route('home') }}" class="accent-border rounded-full px-4 py-1.5 hover:text-white">Strona główna</a>
                    <a href="{{ route('admin.quizzes.index') }}" class="accent-border rounded-full px-4 py-1.5 hover:text-white {{ request()->routeIs('admin.quizzes.*') ? 'bg-white/10 text-white' : '' }}">Quizy</a>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="accent-border rounded-full px-4 py-1.5 hover:text-white">Wyloguj</button>
                    </form>
                </nav>
            </header>

            {{-- Komunikat statusu (sukces) wyświetlany na górze strony --}}
            @if (session('status'))
                <div class="shell border border-emerald-500/20 px-4 py-3 text-sm text-emerald-300">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Wyświetlanie błędów walidacji, jeśli występują --}}
            @if ($errors->any())
                <div class="shell border border-rose-500/20 px-4 py-3">
                    <p class="text-sm font-semibold text-rose-300 mb-2">Wystąpiły błędy:</p>
                    <ul class="list-disc list-inside text-sm text-rose-200 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Obszar główny - każdy widok wstawia tutaj swoją zawartość przez @yield('content') --}}
            <main class="flex-1 shell p-6 md:p-8">
                @yield('content')
            </main>
        </div>

        @stack('scripts')
    </body>
</html>

