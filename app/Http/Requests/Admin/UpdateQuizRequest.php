<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuizRequest extends FormRequest
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
