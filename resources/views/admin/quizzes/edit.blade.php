@extends('layouts.admin', ['title' => 'Edytuj quiz'])

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-3xl font-semibold text-white">Edytuj quiz</h1>
            <p class="text-slate-400 mt-1">Zaktualizuj informacje o quizie i zarządzaj pytaniami</p>
        </div>

        <form action="{{ route('admin.quizzes.update', $quiz) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-semibold text-slate-200 mb-2">
                        Tytuł quizu <span class="text-rose-400">*</span>
                    </label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title', $quiz->title) }}"
                        required
                        class="w-full rounded-2xl border border-white/10 bg-white/5 p-4 text-base text-white placeholder:text-slate-400 focus:border-indigo-400 focus:outline-none"
                        placeholder="Np. Quiz o historii Polski"
                    >
                    @error('title')
                        <p class="mt-1 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-semibold text-slate-200 mb-2">
                        Opis quizu <span class="text-rose-400">*</span>
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        required
                        class="w-full rounded-2xl border border-white/10 bg-white/5 p-4 text-base text-white placeholder:text-slate-400 focus:border-indigo-400 focus:outline-none"
                        placeholder="Opisz quiz, jego tematykę i cel..."
                    >{{ old('description', $quiz->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="time_limit" class="block text-sm font-semibold text-slate-200 mb-2">
                        Limit czasu (w minutach)
                    </label>
                    <input
                        type="number"
                        id="time_limit"
                        name="time_limit"
                        value="{{ old('time_limit', $quiz->time_limit) }}"
                        min="1"
                        max="300"
                        class="w-full rounded-2xl border border-white/10 bg-white/5 p-4 text-base text-white placeholder:text-slate-400 focus:border-indigo-400 focus:outline-none"
                        placeholder="Opcjonalnie - pozostaw puste, jeśli bez limitu"
                    >
                    @error('time_limit')
                        <p class="mt-1 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3">
                    <input
                        type="checkbox"
                        id="is_active"
                        name="is_active"
                        value="1"
                        {{ old('is_active', $quiz->is_active) ? 'checked' : '' }}
                        class="h-5 w-5 accent-indigo-500"
                    >
                    <label for="is_active" class="text-sm font-semibold text-slate-200">
                        Quiz aktywny (widoczny dla użytkowników)
                    </label>
                </div>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('admin.quizzes.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-6 py-3 text-base font-semibold text-white hover:bg-white/10 transition">
                    Anuluj
                </a>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-500/90 px-6 py-3 text-base font-semibold text-white hover:bg-indigo-500 transition">
                    Zaktualizuj quiz
                </button>
            </div>
        </form>

        {{-- Sekcja zarządzania pytaniami --}}
        <div class="mt-8 pt-8 border-t border-white/10">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-semibold text-white">Pytania w quizie</h2>
                    <p class="text-slate-400 mt-1">Zarządzaj pytaniami tego quizu</p>
                </div>
                <a href="{{ route('admin.quizzes.questions.create', $quiz) }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-500/90 px-6 py-3 text-base font-semibold text-white hover:bg-indigo-500 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Dodaj nowe pytanie
                </a>
            </div>

            @if($quiz->questions->count() > 0)
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
                                    @elseif($question->is_open && $question->correct_answer_text)
                                        <div class="mt-3 p-3 rounded-xl bg-blue-500/10 border border-blue-500/20">
                                            <p class="text-sm font-semibold text-blue-300 mb-1">Poprawna odpowiedź:</p>
                                            <p class="text-white text-sm">{{ $question->correct_answer_text }}</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.quizzes.questions.edit', [$quiz, $question]) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-indigo-500/30 bg-indigo-500/10 text-indigo-300 hover:bg-indigo-500/20 transition text-sm font-semibold" title="Edytuj pytanie">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edytuj
                                    </a>
                                    <form action="{{ route('admin.quizzes.questions.destroy', [$quiz, $question]) }}" method="POST" class="inline" onsubmit="return confirm('Czy na pewno chcesz usunąć to pytanie?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-rose-400 transition rounded-xl hover:bg-rose-500/10" title="Usuń">
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
            @else
                <div class="text-center py-12 rounded-2xl border border-white/10 bg-white/5">
                    <p class="text-slate-400 mb-4">Ten quiz nie ma jeszcze żadnych pytań.</p>
                    <a href="{{ route('admin.quizzes.questions.create', $quiz) }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-500/90 px-6 py-3 text-base font-semibold text-white hover:bg-indigo-500 transition">
                        Dodaj pierwsze pytanie
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
