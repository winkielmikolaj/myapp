<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Tworzy użytkownika administratora w bazie danych.
     */
    public function run(): void
    {
        // Sprawdź czy administrator już istnieje
        $adminExists = User::where('email', 'admin@quizapp.local')->exists();

        if (!$adminExists) {
            User::create([
                'name' => 'Administrator',
                'email' => 'admin@quizapp.local',
                'password' => Hash::make('admin123'),
                'is_admin' => true,
            ]);

            $this->command->info('Administrator został utworzony!');
            $this->command->info('Email: admin@quizapp.local');
            $this->command->info('Hasło: admin123');
        } else {
            $this->command->warn('Administrator już istnieje w bazie danych.');
        }
    }
}
