<?php

namespace App\Http\Controllers;

use App\Models\Quiz;

/**
 * Kontroler obsługujący stronę główną aplikacji.
 * 
 * Wyświetla informacje o aplikacji i liczbę dostępnych aktywnych quizów.
 */
class HomeController extends Controller
{
    /**
     * Wyświetla stronę główną z licznikiem dostępnych quizów.
     * 
     * Pobiera liczbę aktywnych quizów z bazy danych i przekazuje ją do widoku.
     * Widok wykorzystuje tę informację w sekcji hero do wyświetlenia statystyk.
     * 
     * @return \Illuminate\View\View Widok strony głównej
     */
    public function index()
    {
        // Pobranie liczby aktywnych quizów (tylko te z is_active = true)
        $quizCount = Quiz::active()->count();

        // Przekazanie danych do widoku
        return view('welcome', compact('quizCount'));
    }
}
