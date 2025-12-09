{{-- 
    Widok wyświetlający pojedyncze pytanie w quizie.
    Użytkownik widzi jedno pytanie na raz i może nawigować między pytaniami.
    Obsługuje zarówno pytania otwarte (textarea) jak i zamknięte (radio buttons).
--}}
@php
    // Obliczenie, czy to ostatnie pytanie w quizie
    $isLastQuestion = $currentStep === $totalQuestions;
    // Obliczenie postępu w procentach (0-100)
    $progress = round(($currentStep / $totalQuestions) * 100);
@endphp

@extends('layouts.app', ['title' => $quiz->title])

@section('content')
    <section class="space-y-10">
        {{-- Pasek informacyjny z nazwą quizu i postępem --}}
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between" data-reveal>
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Tryb quizu</p>
                <h1 class="text-4xl font-semibold text-white">{{ $quiz->title }}</h1>
                <p class="text-slate-300">Pytanie {{ $currentStep }} z {{ $totalQuestions }}</p>
            </div>
            <div class="flex flex-wrap gap-4">
                <div class="shell rounded-2xl px-5 py-4 text-center">
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Postęp</p>
                    <p class="text-3xl font-semibold text-white">{{ $progress }}%</p>
                </div>
                @if ($quiz->time_limit)
                    <div class="shell rounded-2xl px-5 py-4 text-center" data-timer-wrapper>
                        <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Timer</p>
                        <p class="text-3xl font-semibold text-white" data-timer data-remaining="{{ $quiz->time_limit * 60 }}">--:--</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Karta pojedynczego pytania wraz z odpowiedziami --}}
        <div class="shell rounded-[28px] p-6 space-y-6" data-reveal>
            <p class="text-sm text-slate-300">Wartość pytania: <span class="font-semibold text-white">{{ $question->points }} pkt</span></p>
            <form method="POST" action="{{ route('quizzes.submit', $quiz) }}" class="space-y-8">
                @csrf
                {{-- Ukryte pola formularza przekazujące dane do kontrolera --}}
                <input type="hidden" name="question_id" value="{{ $question->id }}">
                <input type="hidden" name="step" value="{{ $currentStep }}">
                <input type="hidden" name="is_final" value="{{ $isLastQuestion ? 1 : 0 }}">

                {{-- Treść pytania i pasek postępu --}}
                <div class="space-y-4">
                    <h2 class="text-2xl font-semibold text-white">{{ $question->question_text }}</h2>
                    {{-- Wizualny pasek postępu pokazujący, ile pytań zostało rozwiązanych --}}
                    <div class="h-2 w-full rounded-full bg-white/5">
                        <div class="h-full rounded-full bg-indigo-500/80 transition-all" style="width: {{ $progress }}%"></div>
                    </div>
                </div>

                {{-- Warunkowe wyświetlanie - pytania otwarte vs zamknięte --}}
                @if ($question->is_open)
                    {{-- Pole tekstowe dla pytań otwartych - użytkownik wpisuje odpowiedź --}}
                    <div class="space-y-3">
                        <label for="answer_text" class="text-sm font-semibold text-slate-200">Twoja odpowiedź</label>
                        <textarea
                            id="answer_text"
                            name="answer_text"
                            rows="4"
                            class="w-full rounded-2xl border border-white/10 bg-white/5 p-4 text-base text-white placeholder:text-slate-400 focus:border-indigo-400 focus:outline-none"
                            required>{{ old('answer_text', $typedAnswer) }}</textarea>
                        {{-- Wyświetlanie błędów walidacji dla pola answer_text --}}
                        @error('answer_text')
                            <p class="text-sm text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>
                @else
                    {{-- Lista odpowiedzi dla pytań zamkniętych - użytkownik wybiera jedną opcję --}}
                    <fieldset class="grid gap-4">
                        <legend class="sr-only">Odpowiedzi</legend>
                        @foreach ($question->answers as $answer)
                            <label class="flex cursor-pointer items-center gap-4 rounded-2xl border border-white/10 bg-white/5 px-5 py-4 text-slate-100 transition hover:bg-white/10">
                                <input
                                    type="radio"
                                    name="answer_id"
                                    value="{{ $answer->id }}"
                                    class="h-5 w-5 accent-indigo-500"
                                    {{ (string) old('answer_id', $selectedAnswerId) === (string) $answer->id ? 'checked' : '' }}
                                    required
                                >
                                <span class="text-base">{{ $answer->answer_text }}</span>
                            </label>
                        @endforeach
                        @error('answer_id')
                            <p class="text-sm text-rose-300">{{ $message }}</p>
                        @enderror
                    </fieldset>
                @endif

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <a href="{{ route('quizzes.index') }}" class="text-sm font-medium text-slate-400 hover:text-white">
                        Wróć do listy
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-500/90 px-6 py-3 text-base font-semibold text-white hover:bg-indigo-500">
                        {{ $isLastQuestion ? 'Zakończ quiz' : 'Następne pytanie' }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" stroke="currentColor">
                            <path d="M5 12h14M12 5l7 7-7 7" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection

{{-- Skrypt obsługujący timer quizu (jeśli quiz ma limit czasu) --}}
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Znajdź element timera na stronie
            const timer = document.querySelector('[data-timer]');
            if (!timer) return; // Jeśli nie ma timera, zakończ

            // Pobierz pozostały czas w sekundach z atrybutu data-remaining
            let remaining = parseInt(timer.dataset.remaining, 10);
            if (Number.isNaN(remaining) || remaining <= 0) {
                timer.textContent = '00:00';
                return;
            }

            const wrapper = timer.closest('[data-timer-wrapper]');
            
            // Funkcja aktualizująca wyświetlanie timera co sekundę
            const update = () => {
                // Oblicz minuty i sekundy z pozostałego czasu
                const minutes = String(Math.max(0, Math.floor(remaining / 60))).padStart(2, '0');
                const seconds = String(Math.max(0, remaining % 60)).padStart(2, '0');
                timer.textContent = `${minutes}:${seconds}`;

                // Jeśli czas się skończył, zmień styl na czerwony i zatrzymaj timer
                if (remaining <= 0) {
                    wrapper?.classList.add('border-rose-400/30', 'bg-rose-500/10', 'text-rose-200');
                    clearInterval(interval);
                    return;
                }
                remaining -= 1; // Zmniejsz pozostały czas o 1 sekundę
            };

            // Uruchom timer od razu i aktualizuj co sekundę
            update();
            const interval = setInterval(update, 1000);
        });
    </script>
@endpush

