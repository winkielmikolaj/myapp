@extends('layouts.admin', ['title' => 'Szczegóły pytania'])

@section('content')
    <div class="space-y-6">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-400 mb-2">
                <a href="{{ route('admin.quizzes.index') }}" class="hover:text-white">Quizy</a>
                <span>/</span>
                <a href="{{ route('admin.quizzes.show', $quiz) }}" class="hover:text-white">{{ $quiz->title }}</a>
                <span>/</span>
                <span>Szczegóły pytania</span>
            </div>
            <h1 class="text-3xl font-semibold text-white">Szczegóły pytania</h1>
            <p class="text-slate-400 mt-1">Quiz: {{ $quiz->title }}</p>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-white/10 bg-white/5 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-indigo-500/20 text-indigo-300 text-lg font-semibold">
                        {{ $question->order + 1 }}
                    </span>
                    <span class="text-sm text-slate-400">{{ $question->points }} pkt</span>
                    @if($question->is_open)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-500/20 text-blue-300">
                            Pytanie otwarte
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-500/20 text-purple-300">
                            Pytanie zamknięte
                        </span>
                    @endif
                </div>
                <h2 class="text-xl font-semibold text-white mb-4">{{ $question->question_text }}</h2>

                @if($question->is_open)
                    <div class="mt-4 p-4 rounded-xl bg-blue-500/10 border border-blue-500/20">
                        <p class="text-sm font-semibold text-blue-300 mb-2">Poprawna odpowiedź:</p>
                        <p class="text-white">{{ $question->correct_answer_text }}</p>
                    </div>
                @else
                    <div class="mt-4 space-y-2">
                        <p class="text-sm font-semibold text-slate-300 mb-3">Odpowiedzi:</p>
                        @foreach($question->answers as $answer)
                            <div class="flex items-center gap-3 p-3 rounded-xl {{ $answer->is_correct ? 'bg-emerald-500/10 border border-emerald-500/20' : 'bg-white/5 border border-white/10' }}">
                                @if($answer->is_correct)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-sm font-medium text-emerald-300">Poprawna</span>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    <span class="text-sm text-slate-400">Niepoprawna</span>
                                @endif
                                <span class="flex-1 text-white">{{ $answer->answer_text }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('admin.quizzes.questions.index', $quiz) }}" class="inline-flex items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-6 py-3 text-base font-semibold text-white hover:bg-white/10 transition">
                    Powrót do listy
                </a>
                <a href="{{ route('admin.quizzes.questions.edit', [$quiz, $question]) }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-500/90 px-6 py-3 text-base font-semibold text-white hover:bg-indigo-500 transition">
                    Edytuj pytanie
                </a>
            </div>
        </div>
    </div>
@endsection

