<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Klasa walidacji żądania tworzenia nowego pytania.
 * 
 * Sprawdza poprawność danych przed utworzeniem pytania w bazie danych.
 * Obsługuje zarówno pytania otwarte (wymagają correct_answer_text) jak i zamknięte (wymagają odpowiedzi).
 * Wymaga, aby użytkownik był zalogowany i miał uprawnienia administratora.
 */
class StoreQuestionRequest extends FormRequest
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
     * Reguły są dynamiczne - zależą od typu pytania (otwarte/zamknięte):
     * - Pytania otwarte wymagają correct_answer_text
     * - Pytania zamknięte wymagają co najmniej 2 odpowiedzi
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Podstawowe reguły wspólne dla wszystkich pytań
        $rules = [
            'quiz_id' => ['required', 'integer', 'exists:quizzes,id'],  // ID quizu musi istnieć w bazie
            'question_text' => ['required', 'string', 'max:2000'],       // Treść pytania wymagana, max 2000 znaków
            'points' => ['required', 'integer', 'min:1', 'max:100'],     // Punkty wymagane, zakres 1-100
            'order' => ['nullable', 'integer', 'min:0'],                 // Kolejność opcjonalna, min 0
            'is_open' => ['nullable', 'boolean'],                        // Typ pytania opcjonalny, true/false
        ];

        // Dynamiczne reguły w zależności od typu pytania
        // Jeśli pytanie jest otwarte, wymagaj correct_answer_text
        if ($this->input('is_open')) {
            $rules['correct_answer_text'] = ['required', 'string', 'max:2000'];  // Poprawna odpowiedź wymagana
            $rules['answers'] = ['nullable', 'array'];                             // Odpowiedzi nie są wymagane
        } else {
            // Jeśli pytanie zamknięte, wymagaj co najmniej 2 odpowiedzi
            $rules['answers'] = ['required', 'array', 'min:2'];                    // Minimum 2 odpowiedzi
            $rules['answers.*.answer_text'] = ['required', 'string', 'max:500'];   // Każda odpowiedź wymagana, max 500 znaków
            $rules['answers.*.is_correct'] = ['nullable', 'boolean'];              // Flaga poprawności opcjonalna
        }

        return $rules;
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
            'quiz_id.required' => 'Quiz jest wymagany.',
            'quiz_id.exists' => 'Wybrany quiz nie istnieje.',
            'question_text.required' => 'Treść pytania jest wymagana.',
            'question_text.max' => 'Treść pytania nie może przekraczać 2000 znaków.',
            'points.required' => 'Liczba punktów jest wymagana.',
            'points.min' => 'Liczba punktów musi wynosić co najmniej 1.',
            'points.max' => 'Liczba punktów nie może przekraczać 100.',
            'correct_answer_text.required' => 'Poprawna odpowiedź jest wymagana dla pytań otwartych.',
            'answers.required' => 'Dodaj co najmniej 2 odpowiedzi dla pytań zamkniętych.',
            'answers.min' => 'Dodaj co najmniej 2 odpowiedzi.',
            'answers.*.answer_text.required' => 'Treść odpowiedzi jest wymagana.',
        ];
    }
}
