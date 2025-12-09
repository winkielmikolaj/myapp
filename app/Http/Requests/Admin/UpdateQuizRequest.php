<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Klasa walidacji żądania aktualizacji istniejącego quizu.
 * 
 * Sprawdza poprawność danych przed zaktualizowaniem quizu w bazie danych.
 * Wymaga, aby użytkownik był zalogowany i miał uprawnienia administratora.
 */
class UpdateQuizRequest extends FormRequest
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
     * Reguły są identyczne jak w StoreQuizRequest, ponieważ aktualizacja
     * wymaga tych samych danych co tworzenie.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'time_limit' => ['nullable', 'integer', 'min:1', 'max:300'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
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
