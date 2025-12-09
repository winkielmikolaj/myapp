@extends('layouts.admin', ['title' => 'Edytuj pytanie'])

@section('content')
    <div class="space-y-6">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-400 mb-2">
                <a href="{{ route('admin.quizzes.index') }}" class="hover:text-white">Quizy</a>
                <span>/</span>
                <a href="{{ route('admin.quizzes.show', $quiz) }}" class="hover:text-white">{{ $quiz->title }}</a>
                <span>/</span>
                <span>Edytuj pytanie</span>
            </div>
            <h1 class="text-3xl font-semibold text-white">Edytuj pytanie</h1>
            <p class="text-slate-400 mt-1">Quiz: {{ $quiz->title }}</p>
        </div>

        <form action="{{ route('admin.quizzes.questions.update', [$quiz, $question]) }}" method="POST" class="space-y-6" id="questionForm">
            @csrf
            @method('PUT')

            <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">

            <div class="space-y-4">
                <div>
                    <label for="question_text" class="block text-sm font-semibold text-slate-200 mb-2">
                        Treść pytania <span class="text-rose-400">*</span>
                    </label>
                    <textarea
                        id="question_text"
                        name="question_text"
                        rows="3"
                        required
                        class="w-full rounded-2xl border border-white/10 bg-white/5 p-4 text-base text-white placeholder:text-slate-400 focus:border-indigo-400 focus:outline-none"
                        placeholder="Wpisz treść pytania..."
                    >{{ old('question_text', $question->question_text) }}</textarea>
                    @error('question_text')
                        <p class="mt-1 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="points" class="block text-sm font-semibold text-slate-200 mb-2">
                            Punkty <span class="text-rose-400">*</span>
                        </label>
                        <input
                            type="number"
                            id="points"
                            name="points"
                            value="{{ old('points', $question->points) }}"
                            required
                            min="1"
                            max="100"
                            class="w-full rounded-2xl border border-white/10 bg-white/5 p-4 text-base text-white placeholder:text-slate-400 focus:border-indigo-400 focus:outline-none"
                        >
                        @error('points')
                            <p class="mt-1 text-sm text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="order" class="block text-sm font-semibold text-slate-200 mb-2">
                            Kolejność
                        </label>
                        <input
                            type="number"
                            id="order"
                            name="order"
                            value="{{ old('order', $question->order) }}"
                            min="0"
                            class="w-full rounded-2xl border border-white/10 bg-white/5 p-4 text-base text-white placeholder:text-slate-400 focus:border-indigo-400 focus:outline-none"
                        >
                        @error('order')
                            <p class="mt-1 text-sm text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <input
                        type="checkbox"
                        id="is_open"
                        name="is_open"
                        value="1"
                        {{ old('is_open', $question->is_open) ? 'checked' : '' }}
                        class="h-5 w-5 accent-indigo-500"
                        onchange="toggleQuestionType()"
                    >
                    <label for="is_open" class="text-sm font-semibold text-slate-200">
                        Pytanie otwarte (wymaga odpowiedzi tekstowej)
                    </label>
                </div>

                <div id="openQuestionFields" style="display: {{ old('is_open', $question->is_open) ? 'block' : 'none' }};">
                    <div>
                        <label for="correct_answer_text" class="block text-sm font-semibold text-slate-200 mb-2">
                            Poprawna odpowiedź <span class="text-rose-400">*</span>
                        </label>
                        <textarea
                            id="correct_answer_text"
                            name="correct_answer_text"
                            rows="3"
                            class="w-full rounded-2xl border border-white/10 bg-white/5 p-4 text-base text-white placeholder:text-slate-400 focus:border-indigo-400 focus:outline-none"
                            placeholder="Wpisz poprawną odpowiedź..."
                        >{{ old('correct_answer_text', $question->correct_answer_text) }}</textarea>
                        @error('correct_answer_text')
                            <p class="mt-1 text-sm text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div id="closedQuestionFields" style="display: {{ old('is_open', $question->is_open) ? 'none' : 'block' }};">
                    <div>
                        <label class="block text-sm font-semibold text-slate-200 mb-2">
                            Odpowiedzi <span class="text-rose-400">*</span> (co najmniej 2, zaznacz poprawną)
                        </label>
                        <div id="answersContainer" class="space-y-3">
                            @php
                                $oldAnswers = old('answers', $question->answers->map(function($a) {
                                    return ['answer_text' => $a->answer_text, 'is_correct' => $a->is_correct];
                                })->toArray());
                            @endphp
                            @foreach($oldAnswers as $index => $answer)
                                <div class="answer-item flex gap-3 items-start">
                                    <input
                                        type="text"
                                        name="answers[{{ $index }}][answer_text]"
                                        value="{{ $answer['answer_text'] ?? '' }}"
                                        placeholder="Treść odpowiedzi..."
                                        class="flex-1 rounded-2xl border border-white/10 bg-white/5 p-3 text-base text-white placeholder:text-slate-400 focus:border-indigo-400 focus:outline-none"
                                    >
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input
                                            type="checkbox"
                                            name="answers[{{ $index }}][is_correct]"
                                            value="1"
                                            {{ ($answer['is_correct'] ?? false) ? 'checked' : '' }}
                                            class="h-5 w-5 accent-indigo-500"
                                        >
                                        <span class="text-sm text-slate-300">Poprawna</span>
                                    </label>
                                    <button type="button" onclick="removeAnswer(this)" class="p-2 text-rose-400 hover:text-rose-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" onclick="addAnswer()" class="mt-3 inline-flex items-center gap-2 text-sm text-indigo-400 hover:text-indigo-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Dodaj odpowiedź
                        </button>
                        @error('answers')
                            <p class="mt-1 text-sm text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('admin.quizzes.questions.index', $quiz) }}" class="inline-flex items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-6 py-3 text-base font-semibold text-white hover:bg-white/10 transition">
                    Anuluj
                </a>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-500/90 px-6 py-3 text-base font-semibold text-white hover:bg-indigo-500 transition">
                    Zaktualizuj pytanie
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        let answerIndex = {{ count(old('answers', $question->answers)) }};

        function toggleQuestionType() {
            const isOpen = document.getElementById('is_open').checked;
            document.getElementById('openQuestionFields').style.display = isOpen ? 'block' : 'none';
            document.getElementById('closedQuestionFields').style.display = isOpen ? 'none' : 'block';
            
            if (isOpen) {
                document.getElementById('correct_answer_text').required = true;
            } else {
                document.getElementById('correct_answer_text').required = false;
            }
        }

        function addAnswer() {
            const container = document.getElementById('answersContainer');
            const div = document.createElement('div');
            div.className = 'answer-item flex gap-3 items-start';
            div.innerHTML = `
                <input
                    type="text"
                    name="answers[${answerIndex}][answer_text]"
                    placeholder="Treść odpowiedzi..."
                    class="flex-1 rounded-2xl border border-white/10 bg-white/5 p-3 text-base text-white placeholder:text-slate-400 focus:border-indigo-400 focus:outline-none"
                >
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="answers[${answerIndex}][is_correct]" value="1" class="h-5 w-5 accent-indigo-500">
                    <span class="text-sm text-slate-300">Poprawna</span>
                </label>
                <button type="button" onclick="removeAnswer(this)" class="p-2 text-rose-400 hover:text-rose-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            `;
            container.appendChild(div);
            answerIndex++;
        }

        function removeAnswer(button) {
            const items = document.querySelectorAll('.answer-item');
            if (items.length > 2) {
                button.closest('.answer-item').remove();
            } else {
                alert('Musisz mieć co najmniej 2 odpowiedzi.');
            }
        }
    </script>
    @endpush
@endsection

