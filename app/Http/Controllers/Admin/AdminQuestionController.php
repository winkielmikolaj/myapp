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

/**
 * Kontroler obsługujący zarządzanie pytaniami w panelu administracyjnym.
 * 
 * Zawiera operacje CRUD (Create, Read, Update, Delete) dla pytań.
 * Pytania są zagnieżdżone w quizach (nested resource).
 * Wszystkie metody wymagają uprawnień administratora (middleware 'admin').
 * 
 * Obsługuje zarówno pytania otwarte (is_open = true) jak i zamknięte (is_open = false).
 */
class AdminQuestionController extends Controller
{
    /**
     * Wyświetla listę pytań dla konkretnego quizu.
     * 
     * Pobiera pytania posortowane po kolumnie 'order' z liczbą odpowiedzi.
     * Wyniki są paginowane (15 na stronę).
     * 
     * @param Quiz $quiz Quiz, dla którego wyświetlamy pytania (route model binding)
     * @return View Widok z listą pytań
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
     * 
     * @param Quiz $quiz Quiz, do którego dodajemy pytanie (route model binding)
     * @return View Widok z formularzem tworzenia pytania
     */
    public function create(Quiz $quiz): View
    {
        return view('admin.questions.create', compact('quiz'));
    }

    /**
     * Zapisuje nowe pytanie w bazie danych.
     * 
     * Obsługuje tworzenie zarówno pytań otwartych (z correct_answer_text)
     * jak i zamkniętych (z listą odpowiedzi).
     * Walidacja danych odbywa się automatycznie przez StoreQuestionRequest.
     * 
     * @param StoreQuestionRequest $request Walidowane dane żądania
     * @param Quiz $quiz Quiz, do którego dodajemy pytanie (route model binding)
     * @return RedirectResponse Przekierowanie do listy pytań z komunikatem sukcesu
     */
    public function store(StoreQuestionRequest $request, Quiz $quiz): RedirectResponse
    {
        // Pobranie zwalidowanych danych z żądania
        $validated = $request->validated();
        $answers = $validated['answers'] ?? [];

        // Tworzenie pytania w bazie danych
        $question = Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => $validated['question_text'],
            'points' => $validated['points'],
            'order' => $validated['order'] ?? 0,
            'is_open' => $validated['is_open'] ?? false,
            'correct_answer_text' => $validated['correct_answer_text'] ?? null,
        ]);

        // Jeśli pytanie nie jest otwarte (zamknięte), tworzymy odpowiedzi
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
     * 
     * Weryfikuje, że pytanie należy do quizu (zabezpieczenie przed dostępem do cudzych pytań).
     * Ładuje odpowiedzi dla pytań zamkniętych.
     * 
     * @param Quiz $quiz Quiz, do którego należy pytanie (route model binding)
     * @param Question $question Pytanie do wyświetlenia (route model binding)
     * @return View Widok ze szczegółami pytania
     */
    public function show(Quiz $quiz, Question $question): View
    {
        // Weryfikacja, że pytanie należy do quizu (zabezpieczenie)
        abort_if($question->quiz_id !== $quiz->id, 404);

        // Eager loading odpowiedzi
        $question->load('answers');

        return view('admin.questions.show', compact('quiz', 'question'));
    }

    /**
     * Wyświetla formularz edycji pytania.
     * 
     * Weryfikuje, że pytanie należy do quizu.
     * Ładuje odpowiedzi do wyświetlenia w formularzu.
     * 
     * @param Quiz $quiz Quiz, do którego należy pytanie (route model binding)
     * @param Question $question Pytanie do edycji (route model binding)
     * @return View Widok z formularzem edycji pytania
     */
    public function edit(Quiz $quiz, Question $question): View
    {
        // Weryfikacja, że pytanie należy do quizu (zabezpieczenie)
        abort_if($question->quiz_id !== $quiz->id, 404);

        // Eager loading odpowiedzi
        $question->load('answers');

        return view('admin.questions.edit', compact('quiz', 'question'));
    }

    /**
     * Aktualizuje pytanie w bazie danych.
     * 
     * Obsługuje aktualizację zarówno pytań otwartych jak i zamkniętych.
     * Dla pytań zamkniętych usuwa stare odpowiedzi i tworzy nowe.
     * Walidacja danych odbywa się automatycznie przez UpdateQuestionRequest.
     * 
     * @param UpdateQuestionRequest $request Walidowane dane żądania
     * @param Quiz $quiz Quiz, do którego należy pytanie (route model binding)
     * @param Question $question Pytanie do aktualizacji (route model binding)
     * @return RedirectResponse Przekierowanie do listy pytań z komunikatem sukcesu
     */
    public function update(UpdateQuestionRequest $request, Quiz $quiz, Question $question): RedirectResponse
    {
        // Weryfikacja, że pytanie należy do quizu (zabezpieczenie)
        abort_if($question->quiz_id !== $quiz->id, 404);

        // Pobranie zwalidowanych danych z żądania
        $validated = $request->validated();
        $answers = $validated['answers'] ?? [];

        // Aktualizacja danych pytania
        $question->update([
            'question_text' => $validated['question_text'],
            'points' => $validated['points'],
            'order' => $validated['order'] ?? $question->order,
            'is_open' => $validated['is_open'] ?? false,
            'correct_answer_text' => $validated['correct_answer_text'] ?? null,
        ]);

        // Obsługa odpowiedzi w zależności od typu pytania
        // Jeśli pytanie nie jest otwarte (zamknięte), aktualizujemy odpowiedzi
        if (!$question->is_open && !empty($answers)) {
            // Usuwamy stare odpowiedzi (zastępujemy je nowymi)
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
            // Jeśli pytanie jest otwarte, usuwamy wszystkie odpowiedzi (nie są potrzebne)
            $question->answers()->delete();
        }

        return redirect()
            ->route('admin.quizzes.questions.index', $quiz)
            ->with('status', 'Pytanie zostało pomyślnie zaktualizowane.');
    }

    /**
     * Usuwa pytanie z bazy danych.
     * 
     * Usunięcie pytania powoduje również usunięcie wszystkich powiązanych odpowiedzi
     * (cascade delete przez relacje w bazie danych).
     * 
     * @param Quiz $quiz Quiz, do którego należy pytanie (route model binding)
     * @param Question $question Pytanie do usunięcia (route model binding)
     * @return RedirectResponse Przekierowanie do listy pytań z komunikatem sukcesu
     */
    public function destroy(Quiz $quiz, Question $question): RedirectResponse
    {
        // Weryfikacja, że pytanie należy do quizu (zabezpieczenie)
        abort_if($question->quiz_id !== $quiz->id, 404);

        // Usunięcie pytania (odpowiedzi zostaną usunięte automatycznie przez cascade delete)
        $question->delete();

        return redirect()
            ->route('admin.quizzes.questions.index', $quiz)
            ->with('status', 'Pytanie zostało pomyślnie usunięte.');
    }
}
