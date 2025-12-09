<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model reprezentujący odpowiedź do pytania zamkniętego.
 * 
 * Odpowiedzi są używane tylko dla pytań zamkniętych (is_open = false).
 * Dla pytań otwartych użytkownik wpisuje odpowiedź tekstową, która jest porównywana z correct_answer_text w modelu Question.
 */
class Answer extends Model
{
    use HasFactory;

    /**
     * Pola, które można masowo wypełniać podczas tworzenia/aktualizacji odpowiedzi.
     */
    protected $fillable = [
        'question_id',      // ID pytania, do którego należy odpowiedź
        'answer_text',      // Treść odpowiedzi
        'is_correct',       // Czy ta odpowiedź jest poprawna (true/false)
    ];

    /**
     * Automatyczne rzutowanie typów danych.
     */
    protected $casts = [
        'is_correct' => 'boolean',  // Flaga poprawności jako wartość logiczna
    ];

    /**
     * Każda odpowiedź należy do konkretnego pytania.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
