<?php

/**
 * Plik definiujący wszystkie trasy (routes) aplikacji.
 * 
 * Zawiera trasy dla:
 * - Strony głównej
 * - Autoryzacji (logowanie/wylogowanie)
 * - Przeglądania i rozwiązywania quizów
 * - Panelu administracyjnego (zarządzanie quizami i pytaniami)
 */

use App\Http\Controllers\Admin\AdminQuestionController;
use App\Http\Controllers\Admin\AdminQuizController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

/**
 * Strona startowa z prostym wprowadzeniem i licznikiem quizów.
 * Dostępna dla wszystkich użytkowników (nie wymaga logowania).
 */
Route::get('/', [HomeController::class, 'index'])->name('home');

/**
 * Trasy autoryzacji - logowanie i wylogowanie.
 * 
 * Middleware 'guest' - dostępne tylko dla niezalogowanych użytkowników.
 * Middleware 'auth' - dostępne tylko dla zalogowanych użytkowników.
 */
Route::middleware('guest')->group(function () {
    // Wyświetlenie formularza logowania (GET)
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    // Przetworzenie danych logowania (POST)
    Route::post('/login', [LoginController::class, 'login']);
});

// Wylogowanie - dostępne tylko dla zalogowanych użytkowników
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

/**
 * Wszystkie trasy związane z doświadczeniem quizowym.
 * 
 * Dostępne dla wszystkich użytkowników (nie wymagają logowania).
 * Grupowanie daje przejrzystość i ułatwia zmianę middleware w przyszłości.
 */
Route::controller(QuizController::class)->group(function () {
    Route::get('/quizzes', 'index')->name('quizzes.index');                    // Lista wszystkich aktywnych quizów
    Route::get('/quizzes/{quiz}', 'show')->name('quizzes.show');               // Wyświetlenie pojedynczego pytania w quizie
    Route::post('/quizzes/{quiz}/submit', 'submit')->name('quizzes.submit');  // Zapisanie odpowiedzi i przejście do następnego pytania
    Route::get('/results/{quiz}', 'results')->name('quizzes.results');        // Wyświetlenie wyników quizu (dane z sesji)
});

/**
 * Trasy panelu administratora - wymagają autoryzacji i uprawnień administratora.
 * 
 * Middleware:
 * - 'auth' - użytkownik musi być zalogowany
 * - 'admin' - użytkownik musi mieć flagę is_admin = true
 * 
 * Prefix 'admin' - wszystkie trasy zaczynają się od /admin
 * Name prefix 'admin.' - wszystkie nazwy tras zaczynają się od 'admin.'
 */
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Trasy CRUD dla quizów (index, create, store, show, edit, update, destroy)
    Route::resource('quizzes', AdminQuizController::class);

    // Trasy CRUD dla pytań (zagnieżdżone w quizach)
    // Scoped route model binding - zapewnia, że pytanie należy do quizu
    Route::resource('quizzes.questions', AdminQuestionController::class)->except(['show'])->scoped([
        'question' => 'id',
    ]);
    // Dodatkowa trasa dla show z scopeBindings (wymusza sprawdzenie relacji quiz-question)
    Route::get('quizzes/{quiz}/questions/{question}', [AdminQuestionController::class, 'show'])
        ->scopeBindings()
        ->name('quizzes.questions.show');
});
