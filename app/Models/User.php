<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model reprezentujący użytkownika systemu.
 * 
 * W tej aplikacji użytkownicy mogą być administratorami (is_admin = true),
 * co daje im dostęp do panelu administracyjnego do zarządzania quizami i pytaniami.
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Pola, które można masowo wypełniać podczas tworzenia/aktualizacji użytkownika.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',         // Imię i nazwisko użytkownika
        'email',        // Adres e-mail (używany do logowania)
        'password',     // Hasło (automatycznie hashowane przez Laravel)
        'is_admin',    // Flaga określająca, czy użytkownik ma uprawnienia administratora
    ];

    /**
     * Pola, które powinny być ukryte podczas serializacji (np. w JSON).
     * Chroni to wrażliwe dane przed przypadkowym wyświetleniem.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',         // Hasło nigdy nie powinno być widoczne w odpowiedziach API
        'remember_token',  // Token "zapamiętaj mnie" również jest wrażliwy
    ];

    /**
     * Definiuje, jak atrybuty powinny być rzutowane podczas odczytu z bazy danych.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',  // Data weryfikacji e-mail jako obiekt DateTime
            'password' => 'hashed',             // Hasło automatycznie hashowane przez Laravel
            'is_admin' => 'boolean',           // Flaga administratora jako wartość logiczna
        ];
    }
}
