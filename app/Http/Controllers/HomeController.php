<?php

namespace App\Http\Controllers;

use App\Models\Quiz;

class HomeController extends Controller
{
    public function index()
    {
        $quizCount = Quiz::active()->count();

        return view('welcome', compact('quizCount'));
    }
}
