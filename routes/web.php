<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

/**
 * Strona startowa z prostym wprowadzeniem i licznikiem quizów.
 */
Route::get('/', [HomeController::class, 'index'])->name('home');

/**
 * Wszystkie trasy związane z doświadczeniem quizowym.
 * Grupowanie daje przejrzystość i ułatwia zmianę middleware w przyszłości.
 */
Route::controller(QuizController::class)->group(function () {
    Route::get('/quizzes', 'index')->name('quizzes.index'); // lista quizów
    Route::get('/quizzes/{quiz}', 'show')->name('quizzes.show'); // pojedynczy quiz
    Route::post('/quizzes/{quiz}/submit', 'submit')->name('quizzes.submit'); // obsługa odpowiedzi
    Route::get('/results/{quiz}', 'results')->name('quizzes.results'); // ekran wyników z sesji
});
