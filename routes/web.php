<?php

use App\Http\Controllers\Admin\AdminQuestionController;
use App\Http\Controllers\Admin\AdminQuizController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

/**
 * Strona startowa z prostym wprowadzeniem i licznikiem quizów.
 */
Route::get('/', [HomeController::class, 'index'])->name('home');

/**
 * Trasy autoryzacji - logowanie i wylogowanie.
 */
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

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

/**
 * Trasy panelu administratora - wymagają autoryzacji i uprawnień administratora.
 */
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Trasy dla quizów
    Route::resource('quizzes', AdminQuizController::class);

    // Trasy dla pytań (zagnieżdżone w quizach) - scoped route model binding
    Route::resource('quizzes.questions', AdminQuestionController::class)->except(['show'])->scoped([
        'question' => 'id',
    ]);
    Route::get('quizzes/{quiz}/questions/{question}', [AdminQuestionController::class, 'show'])
        ->scopeBindings()
        ->name('quizzes.questions.show');
});
