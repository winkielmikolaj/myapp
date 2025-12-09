<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuizRequest;
use App\Http\Requests\Admin\UpdateQuizRequest;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminQuizController extends Controller
{
    /**
     * Wyświetla listę wszystkich quizów.
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
     */
    public function create(): View
    {
        return view('admin.quizzes.create');
    }

    /**
     * Zapisuje nowy quiz w bazie danych.
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
     */
    public function show(Quiz $quiz): View
    {
        $quiz->load(['questions' => function ($query) {
            $query->orderBy('order')->with('answers');
        }]);

        return view('admin.quizzes.show', compact('quiz'));
    }

    /**
     * Wyświetla formularz edycji quizu.
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
     */
    public function update(UpdateQuizRequest $request, Quiz $quiz): RedirectResponse
    {
        $quiz->update($request->validated());

        return redirect()
            ->route('admin.quizzes.edit', $quiz)
            ->with('status', 'Quiz został pomyślnie zaktualizowany.');
    }

    /**
     * Usuwa quiz z bazy danych.
     */
    public function destroy(Quiz $quiz): RedirectResponse
    {
        $quiz->delete();

        return redirect()
            ->route('admin.quizzes.index')
            ->with('status', 'Quiz został pomyślnie usunięty.');
    }
}
