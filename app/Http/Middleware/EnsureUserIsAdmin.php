<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware sprawdzający, czy zalogowany użytkownik ma uprawnienia administratora.
 * 
 * Używany do ochrony tras panelu administracyjnego przed nieautoryzowanym dostępem.
 * Jeśli użytkownik nie jest zalogowany lub nie ma flagi is_admin = true, zwraca błąd 403.
 */
class EnsureUserIsAdmin
{
    /**
     * Obsługuje przychodzące żądanie HTTP.
     * 
     * Sprawdza, czy użytkownik jest zalogowany i czy ma uprawnienia administratora.
     * Jeśli nie, przerywa wykonanie i zwraca błąd 403 (Forbidden).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Sprawdzenie, czy użytkownik jest zalogowany i czy jest administratorem
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Brak dostępu. Wymagane uprawnienia administratora.');
        }

        // Jeśli wszystko OK, kontynuuj przetwarzanie żądania
        return $next($request);
    }
}
