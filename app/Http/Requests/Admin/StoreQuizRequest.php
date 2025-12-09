<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Klasa walidacji żądania tworzenia nowego quizu.
 * 
 * Sprawdza poprawność danych przed utworzeniem quizu w bazie danych.
 * Wymaga, aby użytkownik był zalogowany i miał uprawnienia administratora.
 */
class StoreQuizRequest extends FormRequest
{
    /**
     * Określa, czy użytkownik ma uprawnienia do wykonania tego żądania.
     * 
     * @return bool True, jeśli użytkownik jest zalogowany i jest administratorem
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->is_admin;
    }

    /**
     * Zwraca reguły walidacji dla danych żądania.
     * 
     * Definiuje, jakie pola są wymagane i jakie mają ograniczenia (długość, typ, zakres).
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],           // Tytuł jest wymagany, max 255 znaków
            'description' => ['required', 'string', 'max:2000'],     // Opis jest wymagany, max 2000 znaków
            'time_limit' => ['nullable', 'integer', 'min:1', 'max:300'], // Limit czasu opcjonalny, 1-300 minut
            'is_active' => ['nullable', 'boolean'],                  // Flaga aktywności opcjonalna, true/false
        ];
    }

    /**
     * Zwraca niestandardowe komunikaty błędów walidacji.
     * 
     * Zastępuje domyślne komunikaty Laravel bardziej przyjaznymi dla użytkownika wiadomościami po polsku.
     *
     * @return array<string, string> Tablica z komunikatami błędów
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Tytuł quizu jest wymagany.',
            'title.max' => 'Tytuł nie może przekraczać 255 znaków.',
            'description.required' => 'Opis quizu jest wymagany.',
            'description.max' => 'Opis nie może przekraczać 2000 znaków.',
            'time_limit.integer' => 'Limit czasu musi być liczbą całkowitą.',
            'time_limit.min' => 'Limit czasu musi wynosić co najmniej 1 minutę.',
            'time_limit.max' => 'Limit czasu nie może przekraczać 300 minut.',
        ];
    }
}
