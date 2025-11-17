@extends('layouts.app', ['title' => 'Wyniki - ' . $quiz->title])

@section('content')
    <section class="space-y-8">
        {{-- Nagłówek wyników + szybkie statystyki --}}
        <div class="shell rounded-[28px] p-6" data-reveal>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Podsumowanie</p>
                    <h1 class="text-3xl font-semibold text-white">{{ $quiz->title }}</h1>
                    <p class="text-sm text-slate-300">Twoje odpowiedzi w spokojnym widoku</p>
                </div>
                <div class="flex flex-wrap gap-3 text-center text-sm text-slate-300">
                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                        <p class="text-2xl font-semibold text-emerald-400">{{ $result['total_correct'] }}</p>
                        <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Poprawne</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                        <p class="text-2xl font-semibold text-rose-300">{{ $result['total_questions'] - $result['total_correct'] }}</p>
                        <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Niepoprawne</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                        <p class="text-2xl font-semibold text-cyan-300">{{ $result['percentage'] }}%</p>
                        <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Procent</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Zestawienie szczegółowe wszystkich odpowiedzi --}}
        <div class="shell overflow-hidden rounded-[28px]" data-reveal>
            <table class="w-full divide-y divide-white/5 text-sm text-slate-100">
                <thead class="bg-white/5 text-xs uppercase tracking-[0.3em] text-slate-400">
                    <tr>
                        <th class="px-5 py-4 text-left">Pytanie</th>
                        <th class="px-5 py-4 text-left">Twoja odpowiedź</th>
                        <th class="px-5 py-4 text-left">Poprawna odpowiedź</th>
                        <th class="px-5 py-4 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach ($result['details'] as $detail)
                        <tr>
                            <td class="px-5 py-4 align-top text-left break-words">
                                <p class="font-semibold text-white">{{ $detail['question_text'] }}</p>
                                <p class="text-xs text-slate-400">Wartość: {{ $detail['points'] }} pkt</p>
                            </td>
                            <td class="px-5 py-4 align-top text-slate-200 whitespace-pre-line break-words">
                                {{ $detail['selected_answer'] ?? '—' }}
                            </td>
                            <td class="px-5 py-4 align-top text-slate-200 whitespace-pre-line break-words">
                                {{ $detail['correct_answer'] ?? '—' }}
                            </td>
                            <td class="px-5 py-4 text-right align-top text-sm font-semibold {{ $detail['is_correct'] ? 'text-emerald-300' : 'text-rose-300' }}">
                                {{ $detail['is_correct'] ? 'Poprawnie' : 'Źle' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex flex-wrap gap-4" data-reveal>
            <a href="{{ route('quizzes.index') }}" class="rounded-2xl border border-white/15 px-5 py-2.5 text-sm font-semibold text-slate-200 hover:text-white">
                Wróć do listy
            </a>
            <a href="{{ route('quizzes.show', $quiz) }}" class="inline-flex items-center gap-2 rounded-2xl bg-indigo-500/85 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500">
                Spróbuj ponownie
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor">
                    <path d="M5 12h14M12 5l7 7-7 7" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
        </div>
    </section>
@endsection

