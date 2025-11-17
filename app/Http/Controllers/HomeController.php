<?php

namespace App\Http\Controllers;

use App\Models\Quiz;

class HomeController extends Controller
{
    /**
     * Wyświetla stronę główną z licznikiem dostępnych quizów.
     * Widok wykorzystuje tę informację w sekcji hero.
     */
    public function index()
    {
        $quizCount = Quiz::active()->count();

        return view('welcome', compact('quizCount'));
    }
}
