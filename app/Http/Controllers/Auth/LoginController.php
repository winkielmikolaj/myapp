<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Wyświetla formularz logowania.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Obsługuje próbę logowania.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Sprawdź czy użytkownik jest administratorem
            if (Auth::user()->is_admin) {
                return redirect()->intended(route('admin.quizzes.index'))
                    ->with('status', 'Zalogowano pomyślnie jako administrator.');
            }

            // Jeśli nie jest administratorem, wyloguj go
            Auth::logout();
            return back()->withErrors([
                'email' => 'Brak uprawnień administratora.',
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'Podane dane logowania są nieprawidłowe.',
        ])->onlyInput('email');
    }

    /**
     * Wylogowuje użytkownika.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('status', 'Wylogowano pomyślnie.');
    }
}
