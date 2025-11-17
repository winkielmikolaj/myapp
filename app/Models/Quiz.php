<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;

    /**
     * Pola, które można masowo wypełniać podczas seedowania lub CRUD-u.
     */
    protected $fillable = [
        'title',
        'description',
        'time_limit',
        'is_active',
    ];

    /**
     * Automatyczne rzutowanie – ułatwia pracę z wartościami null/int/bool.
     */
    protected $casts = [
        'time_limit' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Lokalny scope zwracający tylko aktywne quizy.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Pytania należące do quizu uporządkowane po kolumnie `order`.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }
}
