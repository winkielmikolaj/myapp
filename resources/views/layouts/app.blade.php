<!DOCTYPE html>
{{-- 
    Główny layout aplikacji dla użytkowników końcowych.
    Używany przez wszystkie widoki związane z przeglądaniem i rozwiązywaniem quizów.
    Zawiera nagłówek z nawigacją, obszar na komunikaty i sekcję główną na treść.
--}}
<html lang="pl">
    <head>
        {{-- Podstawowe meta tagi --}}
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'QuizApp' }}</title>
        {{-- Sprawdzenie, czy aplikacja używa Vite (dev) czy CDN (production) --}}
        @php
            $hasViteBuild = file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'));
        @endphp

        {{-- Ładowanie zasobów CSS/JS - Vite w trybie dev, CDN w production --}}
        @if ($hasViteBuild)
            {{-- Użyj Vite do kompilacji zasobów (tryb deweloperski) --}}
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            {{-- Fallback do CDN (tryb produkcyjny bez Vite) --}}
            <link rel="preconnect" href="https://fonts.bunny.net">
            <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" rel="stylesheet" />
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.13/dist/tailwind.min.css">
        @endif

        {{-- Style CSS dla ciemnego motywu aplikacji --}}
        <style>
            /* Ustawienia podstawowe - ciemny motyw, font Instrument Sans */
            :root {
                font-family: 'Instrument Sans', system-ui, sans-serif;
                color-scheme: dark;
            }
            /* Tło z gradientem radialnym dla efektu głębi */
            body {
                background: radial-gradient(circle at top, #111827, #0f172a 45%, #0b1121);
                color: #e2e8f0;
            }
            /* Klasa .shell - uniwersalny kontener dla kart z efektem szkła (glassmorphism) */
            .shell {
                background: rgba(15, 23, 42, 0.7);
                border: 1px solid rgba(148, 163, 184, 0.08);
                border-radius: 28px;
                box-shadow: 0 20px 40px rgba(2, 6, 23, 0.35);
                backdrop-filter: blur(14px);
            }
            /* Klasa .accent-border - obramowanie w kolorze akcentu (indigo) */
            .accent-border {
                border: 1px solid rgba(99, 102, 241, 0.25);
            }
            /* Animacja reveal - elementy pojawiają się z dołu z efektem fade-in */
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
            {{-- Nagłówek z logo, opisem i nawigacją --}}
            <header class="shell flex flex-col gap-4 p-6 md:flex-row md:items-center md:justify-between">
                {{-- Logo i opis aplikacji --}}
                <div>
                    <a href="{{ route('home') }}" class="text-2xl font-semibold text-white">QuizApp</a>
                    <p class="text-sm text-slate-400">Lekki i nowoczesny interfejs quizowy</p>
                </div>
                {{-- Nawigacja główna - różne opcje dla zalogowanych i niezalogowanych użytkowników --}}
                <nav class="flex flex-wrap gap-3 text-sm text-slate-300">
                    <a href="{{ route('home') }}" class="accent-border rounded-full px-4 py-1.5 hover:text-white {{ request()->routeIs('home') ? 'bg-white/10 text-white' : '' }}">Strona główna</a>
                    <a href="{{ route('quizzes.index') }}" class="accent-border rounded-full px-4 py-1.5 hover:text-white {{ request()->routeIs('quizzes.*') ? 'bg-white/10 text-white' : '' }}">Quizy</a>
                    {{-- Opcje dla zalogowanych administratorów --}}
                    @auth
                        @if(auth()->user()->is_admin)
                            <a href="{{ route('admin.quizzes.index') }}" class="accent-border rounded-full px-4 py-1.5 hover:text-white {{ request()->routeIs('admin.*') ? 'bg-white/10 text-white' : '' }}">Panel Admin</a>
                            {{-- Formularz wylogowania --}}
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="accent-border rounded-full px-4 py-1.5 hover:text-white">Wyloguj</button>
                            </form>
                        @endif
                    @else
                        {{-- Link do logowania dla niezalogowanych --}}
                        <a href="{{ route('login') }}" class="accent-border rounded-full px-4 py-1.5 hover:text-white {{ request()->routeIs('login') ? 'bg-white/10 text-white' : '' }}">Logowanie</a>
                    @endauth
                </nav>
            </header>

            {{-- Komunikat statusu (sukces, błąd) wyświetlany na górze strony --}}
            @if (session('status'))
                <div class="shell reveal border border-emerald-500/20 px-4 py-3 text-sm text-emerald-300">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Obszar główny - każdy widok wstawia tutaj swoją zawartość przez @yield('content') --}}
            <main class="flex-1 shell p-6 md:p-8">
                @yield('content')
            </main>
        </div>

        {{-- Stack dla skryptów JavaScript - widoki mogą dodawać skrypty przez @push('scripts') --}}
        @stack('scripts')

        {{-- Skrypt obsługujący animację reveal - elementy pojawiają się podczas przewijania --}}
        <script>
            // Intersection Observer do wykrywania, kiedy elementy wchodzą w widok
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        // Dodaj klasę reveal-visible, gdy element jest widoczny
                        entry.target.classList.add('reveal-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.2 }); // Element musi być widoczny w 20% aby się pojawić

            // Znajdź wszystkie elementy z atrybutem data-reveal i dodaj do nich animację
            document.querySelectorAll('[data-reveal]').forEach((el) => {
                el.classList.add('reveal');
                observer.observe(el);
            });
        </script>
    </body>
</html>

