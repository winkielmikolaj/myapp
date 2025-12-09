<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question_text',
        'points',
        'order',
        'is_open',
        'correct_answer_text',
    ];

    protected $casts = [
        'points' => 'integer',
        'order' => 'integer',
        'is_open' => 'boolean',
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
