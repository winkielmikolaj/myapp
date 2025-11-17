@extends('layouts.app', ['title' => 'Quizy'])

@section('content')
    <section class="space-y-10">
        <div class="shell rounded-[28px] p-6" data-reveal>
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="space-y-4">
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Lista quizów</p>
                    <h1 class="text-3xl font-semibold text-white">Wybierz quiz i rozpocznij spokojną sesję.</h1>
                    <p class="text-base text-slate-300">Stonowany ciemny motyw, delikatne gradienty i brak zbędnych efektów – wszystko skupione na treści.</p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/5 p-5 text-center">
                    <p class="text-sm uppercase tracking-[0.25em] text-slate-400">Aktywne</p>
                    <p class="mt-2 text-4xl font-semibold text-white">{{ $quizzes->count() }}</p>
                    <p class="text-xs text-slate-400">quizy w bazie</p>
                </div>
            </div>
        </div>

        @if ($quizzes->isEmpty())
            <div class="shell rounded-3xl p-8 text-center text-slate-300" data-reveal>
                Brak aktywnych quizów – dodaj nowe rekordy w seederze.
            </div>
        @else
            <div class="grid gap-6 md:grid-cols-2" data-reveal>
                @foreach ($quizzes as $quiz)
                    <article class="shell flex flex-col gap-5 rounded-3xl p-6 transition hover:-translate-y-0.5">
                        <div class="flex flex-col gap-3">
                            <p class="text-xs uppercase tracking-[0.35em] text-slate-400">{{ $quiz->is_active ? 'Aktywny' : 'W przygotowaniu' }}</p>
                            <h2 class="text-2xl font-semibold text-white">{{ $quiz->title }}</h2>
                            <p class="text-sm leading-relaxed text-slate-300">{{ $quiz->description }}</p>
                        </div>
                        <div class="grid gap-3 text-sm text-slate-300 md:grid-cols-3">
                            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-center">
                                <p class="text-xl font-semibold text-white">{{ $quiz->questions_count }}</p>
                                <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Pytania</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-center">
                                <p class="text-xl font-semibold text-white">{{ $quiz->time_limit ? $quiz->time_limit . ' min' : 'Brak' }}</p>
                                <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Limit</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-center">
                                <p class="text-xl font-semibold text-white">{{ $quiz->total_points ?? '∞' }}</p>
                                <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Punkty</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center justify-between gap-4 text-sm text-slate-400">
                            <span>Dodano: {{ $quiz->created_at?->format('d.m.Y') }}</span>
                            <a href="{{ route('quizzes.show', $quiz) }}" class="inline-flex items-center gap-2 rounded-2xl bg-indigo-500/85 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500">
                                Rozpocznij
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor">
                                    <path d="M5 12h14M12 5l7 7-7 7" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection

