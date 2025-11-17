<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::controller(QuizController::class)->group(function () {
    Route::get('/quizzes', 'index')->name('quizzes.index');
    Route::get('/quizzes/{quiz}', 'show')->name('quizzes.show');
    Route::post('/quizzes/{quiz}/submit', 'submit')->name('quizzes.submit');
    Route::get('/results/{quiz}', 'results')->name('quizzes.results');
});
