<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'QuizApp' }}</title>
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
            .reveal {
                opacity: 0;
                transform: translateY(16px);
                transition: opacity 0.6s ease, transform 0.6s ease;
            }
            .reveal-visible {
                opacity: 1;
                transform: translateY(0);
            }
        </style>
    </head>
    <body class="min-h-screen px-4 py-8 text-slate-100 antialiased md:px-6">
        {{-- Główna kolumna aplikacji: nagłówek + karta z kontentem --}}
        <div class="mx-auto flex min-h-screen w-full max-w-5xl flex-col gap-8">
            <header class="shell flex flex-col gap-4 p-6 md:flex-row md:items-center md:justify-between">
                <div>
                    <a href="{{ route('home') }}" class="text-2xl font-semibold text-white">QuizApp</a>
                    <p class="text-sm text-slate-400">Lekki i nowoczesny interfejs quizowy</p>
                </div>
                <nav class="flex flex-wrap gap-3 text-sm text-slate-300">
                    <a href="{{ route('home') }}" class="accent-border rounded-full px-4 py-1.5 hover:text-white {{ request()->routeIs('home') ? 'bg-white/10 text-white' : '' }}">Strona główna</a>
                    <a href="{{ route('quizzes.index') }}" class="accent-border rounded-full px-4 py-1.5 hover:text-white {{ request()->routeIs('quizzes.*') ? 'bg-white/10 text-white' : '' }}">Quizy</a>
                </nav>
            </header>

            @if (session('status'))
                <div class="shell reveal border border-emerald-500/20 px-4 py-3 text-sm text-emerald-300">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Obszar, w którym każdy widok wstawia własną zawartość --}}
            <main class="flex-1 shell p-6 md:p-8">
                @yield('content')
            </main>
        </div>

        @stack('scripts')

        <script>
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('reveal-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.2 });

            document.querySelectorAll('[data-reveal]').forEach((el) => {
                el.classList.add('reveal');
                observer.observe(el);
            });
        </script>
    </body>
</html>

