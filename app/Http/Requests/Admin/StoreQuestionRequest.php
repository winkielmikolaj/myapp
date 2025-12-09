<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->is_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'quiz_id' => ['required', 'integer', 'exists:quizzes,id'],
            'question_text' => ['required', 'string', 'max:2000'],
            'points' => ['required', 'integer', 'min:1', 'max:100'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_open' => ['nullable', 'boolean'],
        ];

        // Jeśli pytanie jest otwarte, wymagaj correct_answer_text
        if ($this->input('is_open')) {
            $rules['correct_answer_text'] = ['required', 'string', 'max:2000'];
            $rules['answers'] = ['nullable', 'array'];
        } else {
            // Jeśli pytanie zamknięte, wymagaj odpowiedzi
            $rules['answers'] = ['required', 'array', 'min:2'];
            $rules['answers.*.answer_text'] = ['required', 'string', 'max:500'];
            $rules['answers.*.is_correct'] = ['nullable', 'boolean'];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
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
