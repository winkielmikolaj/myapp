@extends('layouts.admin', ['title' => $quiz->title])

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-semibold text-white">{{ $quiz->title }}</h1>
                <p class="text-slate-400 mt-1">{{ $quiz->description }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-white/10 bg-white/5 px-6 py-3 text-base font-semibold text-white hover:bg-white/10 transition">
                    Edytuj quiz
                </a>
                <a href="{{ route('admin.quizzes.questions.create', $quiz) }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-500/90 px-6 py-3 text-base font-semibold text-white hover:bg-indigo-500 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Dodaj pytanie
                </a>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Pytania</p>
                <p class="text-2xl font-semibold text-white mt-2">{{ $quiz->questions->count() }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Punkty</p>
                <p class="text-2xl font-semibold text-white mt-2">{{ $quiz->questions->sum('points') }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Status</p>
                <p class="text-2xl font-semibold text-white mt-2">
                    @if($quiz->is_active)
                        <span class="text-emerald-300">Aktywny</span>
                    @else
                        <span class="text-slate-400">Nieaktywny</span>
                    @endif
                </p>
            </div>
        </div>

        @if($quiz->questions->count() > 0)
            <div>
                <h2 class="text-xl font-semibold text-white mb-4">Pytania w quizie</h2>
                <div class="space-y-4">
                    @foreach($quiz->questions as $question)
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-500/20 text-indigo-300 text-sm font-semibold">
                                            {{ $question->order + 1 }}
                                        </span>
                                        <span class="text-sm text-slate-400">{{ $question->points }} pkt</span>
                                        @if($question->is_open)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-500/20 text-blue-300">
                                                Otwarte
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-500/20 text-purple-300">
                                                Zamknięte
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-white font-medium mb-2">{{ $question->question_text }}</p>
                                    @if(!$question->is_open && $question->answers->count() > 0)
                                        <div class="mt-3 space-y-2">
                                            @foreach($question->answers as $answer)
                                                <div class="flex items-center gap-2 text-sm text-slate-300">
                                                    @if($answer->is_correct)
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    @endif
                                                    <span>{{ $answer->answer_text }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.quizzes.questions.edit', [$quiz, $question]) }}" class="p-2 text-slate-400 hover:text-indigo-400 transition" title="Edytuj">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.quizzes.questions.destroy', [$quiz, $question]) }}" method="POST" class="inline" onsubmit="return confirm('Czy na pewno chcesz usunąć to pytanie?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-rose-400 transition" title="Usuń">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="text-center py-12 rounded-2xl border border-white/10 bg-white/5">
                <p class="text-slate-400 mb-4">Ten quiz nie ma jeszcze żadnych pytań.</p>
                <a href="{{ route('admin.quizzes.questions.create', $quiz) }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-500/90 px-6 py-3 text-base font-semibold text-white hover:bg-indigo-500 transition">
                    Dodaj pierwsze pytanie
                </a>
            </div>
        @endif
    </div>
@endsection

