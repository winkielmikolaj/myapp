<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Kontroler obsługujący logowanie i wylogowanie użytkowników.
 * 
 * W tej aplikacji tylko użytkownicy z uprawnieniami administratora (is_admin = true)
 * mogą się zalogować. Zwykli użytkownicy nie mają dostępu do systemu.
 */
class LoginController extends Controller
{
    /**
     * Wyświetla formularz logowania dla administratorów.
     * 
     * @return View Widok z formularzem logowania
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Obsługuje próbę logowania użytkownika.
     * 
     * Weryfikuje dane logowania i sprawdza, czy użytkownik ma uprawnienia administratora.
     * Jeśli logowanie się powiedzie i użytkownik jest administratorem, przekierowuje do panelu admin.
     * Jeśli użytkownik nie jest administratorem, wylogowuje go i wyświetla błąd.
     * 
     * @param Request $request Żądanie HTTP zawierające dane logowania (email, password, remember)
     * @return RedirectResponse Przekierowanie po próbie logowania
     */
    public function login(Request $request): RedirectResponse
    {
        // Walidacja danych logowania
        $credentials = $request->validate([
            'email' => ['required', 'email'],      // E-mail jest wymagany i musi być poprawnym adresem
            'password' => ['required', 'string'],  // Hasło jest wymagane
        ]);

        // Próba zalogowania użytkownika
        // Drugi parametr określa, czy zapamiętać użytkownika (checkbox "remember me")
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Regeneracja ID sesji dla bezpieczeństwa (zapobiega atakom session fixation)
            $request->session()->regenerate();

            // Sprawdzenie, czy zalogowany użytkownik ma uprawnienia administratora
            if (Auth::user()->is_admin) {
                // Przekierowanie do panelu administracyjnego
                return redirect()->intended(route('admin.quizzes.index'))
                    ->with('status', 'Zalogowano pomyślnie jako administrator.');
            }

            // Jeśli użytkownik nie jest administratorem, wyloguj go i wyświetl błąd
            Auth::logout();
            return back()->withErrors([
                'email' => 'Brak uprawnień administratora.',
            ])->onlyInput('email');
        }

        // Jeśli dane logowania są nieprawidłowe, zwróć błąd
        return back()->withErrors([
            'email' => 'Podane dane logowania są nieprawidłowe.',
        ])->onlyInput('email');
    }

    /**
     * Wylogowuje użytkownika z systemu.
     * 
     * Kończy sesję użytkownika, unieważnia sesję i regeneruje token CSRF.
     * Następnie przekierowuje na stronę główną.
     * 
     * @param Request $request Żądanie HTTP
     * @return RedirectResponse Przekierowanie na stronę główną
     */
    public function logout(Request $request): RedirectResponse
    {
        // Wylogowanie użytkownika
        Auth::logout();

        // Unieważnienie sesji (usuwa wszystkie dane sesji)
        $request->session()->invalidate();
        
        // Regeneracja tokena CSRF (zapobiega atakom CSRF)
        $request->session()->regenerateToken();

        // Przekierowanie na stronę główną z komunikatem sukcesu
        return redirect()->route('home')
            ->with('status', 'Wylogowano pomyślnie.');
    }
}
