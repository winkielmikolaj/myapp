<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuestionRequest;
use App\Http\Requests\Admin\UpdateQuestionRequest;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminQuestionController extends Controller
{
    /**
     * Wyświetla listę pytań dla konkretnego quizu.
     */
    public function index(Quiz $quiz): View
    {
        $questions = $quiz->questions()
            ->withCount('answers')
            ->orderBy('order')
            ->paginate(15);

        return view('admin.questions.index', compact('quiz', 'questions'));
    }

    /**
     * Wyświetla formularz tworzenia nowego pytania.
     */
    public function create(Quiz $quiz): View
    {
        return view('admin.questions.create', compact('quiz'));
    }

    /**
     * Zapisuje nowe pytanie w bazie danych.
     */
    public function store(StoreQuestionRequest $request, Quiz $quiz): RedirectResponse
    {
        $validated = $request->validated();
        $answers = $validated['answers'] ?? [];

        // Tworzenie pytania
        $question = Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => $validated['question_text'],
            'points' => $validated['points'],
            'order' => $validated['order'] ?? 0,
            'is_open' => $validated['is_open'] ?? false,
            'correct_answer_text' => $validated['correct_answer_text'] ?? null,
        ]);

        // Jeśli pytanie nie jest otwarte, tworzymy odpowiedzi
        if (!$question->is_open && !empty($answers)) {
            foreach ($answers as $answerData) {
                Answer::create([
                    'question_id' => $question->id,
                    'answer_text' => $answerData['answer_text'],
                    'is_correct' => $answerData['is_correct'] ?? false,
                ]);
            }
        }

        return redirect()
            ->route('admin.quizzes.questions.index', $quiz)
            ->with('status', 'Pytanie zostało pomyślnie utworzone.');
    }

    /**
     * Wyświetla szczegóły pytania.
     */
    public function show(Quiz $quiz, Question $question): View
    {
        // Weryfikacja, że pytanie należy do quizu
        abort_if($question->quiz_id !== $quiz->id, 404);

        $question->load('answers');

        return view('admin.questions.show', compact('quiz', 'question'));
    }

    /**
     * Wyświetla formularz edycji pytania.
     */
    public function edit(Quiz $quiz, Question $question): View
    {
        // Weryfikacja, że pytanie należy do quizu
        abort_if($question->quiz_id !== $quiz->id, 404);

        $question->load('answers');

        return view('admin.questions.edit', compact('quiz', 'question'));
    }

    /**
     * Aktualizuje pytanie w bazie danych.
     */
    public function update(UpdateQuestionRequest $request, Quiz $quiz, Question $question): RedirectResponse
    {
        // Weryfikacja, że pytanie należy do quizu
        abort_if($question->quiz_id !== $quiz->id, 404);

        $validated = $request->validated();
        $answers = $validated['answers'] ?? [];

        // Aktualizacja pytania
        $question->update([
            'question_text' => $validated['question_text'],
            'points' => $validated['points'],
            'order' => $validated['order'] ?? $question->order,
            'is_open' => $validated['is_open'] ?? false,
            'correct_answer_text' => $validated['correct_answer_text'] ?? null,
        ]);

        // Jeśli pytanie nie jest otwarte, aktualizujemy odpowiedzi
        if (!$question->is_open && !empty($answers)) {
            // Usuwamy stare odpowiedzi
            $question->answers()->delete();

            // Tworzymy nowe odpowiedzi
            foreach ($answers as $answerData) {
                Answer::create([
                    'question_id' => $question->id,
                    'answer_text' => $answerData['answer_text'],
                    'is_correct' => $answerData['is_correct'] ?? false,
                ]);
            }
        } elseif ($question->is_open) {
            // Jeśli pytanie jest otwarte, usuwamy odpowiedzi
            $question->answers()->delete();
        }

        return redirect()
            ->route('admin.quizzes.questions.index', $quiz)
            ->with('status', 'Pytanie zostało pomyślnie zaktualizowane.');
    }

    /**
     * Usuwa pytanie z bazy danych.
     */
    public function destroy(Quiz $quiz, Question $question): RedirectResponse
    {
        // Weryfikacja, że pytanie należy do quizu
        abort_if($question->quiz_id !== $quiz->id, 404);

        $question->delete();

        return redirect()
            ->route('admin.quizzes.questions.index', $quiz)
            ->with('status', 'Pytanie zostało pomyślnie usunięte.');
    }
}
