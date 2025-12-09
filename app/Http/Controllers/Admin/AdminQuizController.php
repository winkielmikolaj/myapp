<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuizRequest;
use App\Http\Requests\Admin\UpdateQuizRequest;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Kontroler obsługujący zarządzanie quizami w panelu administracyjnym.
 * 
 * Zawiera operacje CRUD (Create, Read, Update, Delete) dla quizów.
 * Wszystkie metody wymagają uprawnień administratora (middleware 'admin').
 */
class AdminQuizController extends Controller
{
    /**
     * Wyświetla listę wszystkich quizów w systemie.
     * 
     * Pobiera quizy z liczbą pytań i sumą punktów, sortuje od najnowszych.
     * Wyniki są paginowane (15 na stronę).
     * 
     * @return View Widok z listą quizów
     */
    public function index(): View
    {
        $quizzes = Quiz::withCount('questions')
            ->withSum('questions as total_points', 'points')
            ->latest()
            ->paginate(15);

        return view('admin.quizzes.index', compact('quizzes'));
    }

    /**
     * Wyświetla formularz tworzenia nowego quizu.
     * 
     * @return View Widok z formularzem tworzenia quizu
     */
    public function create(): View
    {
        return view('admin.quizzes.create');
    }

    /**
     * Zapisuje nowy quiz w bazie danych.
     * 
     * Walidacja danych odbywa się automatycznie przez StoreQuizRequest.
     * Po utworzeniu quizu przekierowuje do listy quizów z komunikatem sukcesu.
     * 
     * @param StoreQuizRequest $request Walidowane dane żądania
     * @return RedirectResponse Przekierowanie do listy quizów
     */
    public function store(StoreQuizRequest $request): RedirectResponse
    {
        $quiz = Quiz::create($request->validated());

        return redirect()
            ->route('admin.quizzes.index')
            ->with('status', 'Quiz został pomyślnie utworzony.');
    }

    /**
     * Wyświetla szczegóły quizu wraz z listą pytań.
     * 
     * Ładuje quiz wraz z pytaniami (posortowanymi po kolumnie 'order')
     * i odpowiedziami dla każdego pytania (eager loading).
     * 
     * @param Quiz $quiz Quiz do wyświetlenia (route model binding)
     * @return View Widok ze szczegółami quizu
     */
    public function show(Quiz $quiz): View
    {
        // Eager loading - załaduj pytania posortowane po kolejności wraz z odpowiedziami
        $quiz->load(['questions' => function ($query) {
            $query->orderBy('order')->with('answers');
        }]);

        return view('admin.quizzes.show', compact('quiz'));
    }

    /**
     * Wyświetla formularz edycji quizu.
     * 
     * Ładuje quiz wraz z pytaniami i odpowiedziami do wyświetlenia w formularzu.
     * 
     * @param Quiz $quiz Quiz do edycji (route model binding)
     * @return View Widok z formularzem edycji quizu
     */
    public function edit(Quiz $quiz): View
    {
        $quiz->load(['questions' => function ($query) {
            $query->orderBy('order')->with('answers');
        }]);

        return view('admin.quizzes.edit', compact('quiz'));
    }

    /**
     * Aktualizuje quiz w bazie danych.
     * 
     * Walidacja danych odbywa się automatycznie przez UpdateQuizRequest.
     * Po aktualizacji przekierowuje do formularza edycji z komunikatem sukcesu.
     * 
     * @param UpdateQuizRequest $request Walidowane dane żądania
     * @param Quiz $quiz Quiz do aktualizacji (route model binding)
     * @return RedirectResponse Przekierowanie do formularza edycji
     */
    public function update(UpdateQuizRequest $request, Quiz $quiz): RedirectResponse
    {
        // Aktualizacja quizu walidowanymi danymi
        $quiz->update($request->validated());

        return redirect()
            ->route('admin.quizzes.edit', $quiz)
            ->with('status', 'Quiz został pomyślnie zaktualizowany.');
    }

    /**
     * Usuwa quiz z bazy danych.
     * 
     * Usunięcie quizu powoduje również usunięcie wszystkich powiązanych pytań i odpowiedzi
     * (cascade delete przez relacje w bazie danych).
     * 
     * @param Quiz $quiz Quiz do usunięcia (route model binding)
     * @return RedirectResponse Przekierowanie do listy quizów z komunikatem sukcesu
     */
    public function destroy(Quiz $quiz): RedirectResponse
    {
        $quiz->delete();

        return redirect()
            ->route('admin.quizzes.index')
            ->with('status', 'Quiz został pomyślnie usunięty.');
    }
}
