@extends('layouts.app', ['title' => 'QuizApp'])

@section('content')
    <section class="space-y-10">
        <div class="grid gap-8 lg:grid-cols-2">
            <div class="space-y-6" data-reveal>
                <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Quizy programistyczne</p>
                <h1 class="text-4xl font-semibold text-white">QuizApp – spokojny, czytelny interfejs do szybkiego sprawdzenia wiedzy.</h1>
                <p class="text-base text-slate-300">
                    Zostawiliśmy subtelne animacje i ciemny motyw, ale ograniczyliśmy efekty specjalne. Dzięki temu korzystanie z quizów jest przyjemne i nie rozprasza.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('quizzes.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-indigo-500/80 px-6 py-3 text-base font-semibold text-white hover:bg-indigo-500">
                        Przeglądaj quizy
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor">
                            <path d="M5 12h14M12 5l7 7-7 7" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>
                    <a href="#flow" class="inline-flex items-center gap-2 rounded-2xl border border-white/10 px-6 py-3 text-base font-semibold text-slate-200 hover:text-white">
                        Jak to działa
                    </a>
                </div>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="shell rounded-2xl p-4 text-center">
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Aktywne</p>
                        <p class="mt-2 text-3xl font-semibold text-white">{{ $quizCount }}</p>
                        <p class="text-xs text-slate-400">quizów</p>
                    </div>
                    <div class="shell rounded-2xl p-4 text-center">
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Średni czas</p>
                        <p class="mt-2 text-3xl font-semibold text-white">10 min</p>
                        <p class="text-xs text-slate-400">na quiz</p>
                    </div>
                    <div class="shell rounded-2xl p-4 text-center">
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Pytania</p>
                        <p class="mt-2 text-3xl font-semibold text-white">20+</p>
                        <p class="text-xs text-slate-400">w bazie</p>
                    </div>
                </div>
            </div>
            <div class="shell rounded-[28px] p-6 space-y-4" data-reveal>
                <h2 class="text-lg font-semibold text-white">Czego się spodziewać?</h2>
                <ul class="space-y-3 text-sm text-slate-300">
                    <li class="flex gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-indigo-400"></span>
                        Płynne przejścia między pytaniami, delikatne animacje i ciemna, ale stonowana paleta.
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-indigo-400"></span>
                        Czytelne tabele wyników z Twoimi odpowiedziami i krótkim podsumowaniem.
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-indigo-400"></span>
                        Responsywność – quizy wyglądają dobrze zarówno na laptopie, jak i na telefonie.
                    </li>
                </ul>
            </div>
        </div>

        <section id="flow" class="space-y-6" data-reveal>
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Przebieg sesji</p>
                <h2 class="text-3xl font-semibold text-white">Jak to działa?</h2>
            </div>
            <div class="grid gap-4 md:grid-cols-3">
                @php
                    $steps = [
                        ['title' => 'Wybierz quiz', 'desc' => 'Lista obejmuje opis, liczbę pytań oraz ewentualny limit czasu.'],
                        ['title' => 'Odpowiadaj po kolei', 'desc' => 'Każde pytanie to osobna karta z prostym licznikiem i jasno oznaczonymi odpowiedziami.'],
                        ['title' => 'Przejrzyj wyniki', 'desc' => 'Podsumowanie pokazuje poprawne odpowiedzi, procent oraz Twoje wybory.'],
                    ];
                @endphp
                @foreach ($steps as $index => $step)
                    <div class="shell rounded-3xl p-5">
                        <p class="text-sm text-slate-400">Krok {{ $index + 1 }}</p>
                        <h3 class="mt-2 text-xl font-semibold text-white">{{ $step['title'] }}</h3>
                        <p class="mt-2 text-sm text-slate-300">{{ $step['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    </section>
@endsection

