<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model reprezentujący pytanie w quizie.
 * 
 * Pytanie może być typu otwartego (is_open = true) lub zamkniętego (is_open = false).
 * Dla pytań otwartych użytkownik wpisuje odpowiedź tekstową, dla zamkniętych wybiera z listy.
 */
class Question extends Model
{
    use HasFactory;

    /**
     * Pola, które można masowo wypełniać podczas tworzenia/aktualizacji pytania.
     */
    protected $fillable = [
        'quiz_id',              // ID quizu, do którego należy pytanie
        'question_text',        // Treść pytania
        'points',               // Liczba punktów za poprawne odpowiedzenie
        'order',                // Kolejność wyświetlania pytania w quizie
        'is_open',              // Czy pytanie jest otwarte (true) czy zamknięte (false)
        'correct_answer_text',  // Poprawna odpowiedź dla pytań otwartych
    ];

    /**
     * Automatyczne rzutowanie typów danych.
     */
    protected $casts = [
        'points' => 'integer',      // Punkty jako liczba całkowita
        'order' => 'integer',       // Kolejność jako liczba całkowita
        'is_open' => 'boolean',     // Typ pytania jako wartość logiczna
    ];

    /**
     * Każde pytanie należy do jednego quizu.
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Pobiera pytanie w kontekście quizu (dla route model binding).
     */
    public function getRouteKeyName(): string
    {
        return 'id';
    }

    /**
     * Scope dla route model binding - zapewnia, że pytanie należy do quizu.
     */
    public function scopeForQuiz($query, Quiz $quiz)
    {
        return $query->where('quiz_id', $quiz->id);
    }

    /**
     * Odpowiedzi dostępne dla pytania.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }
}
