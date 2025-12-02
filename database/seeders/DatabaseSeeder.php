<?php

namespace Database\Seeders;

use App\Models\Quiz;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Zdefiniowane poniżej quizy służą jako bogaty zestaw danych startowych.
        $quizzes = [
            [
                'title' => 'Laravel Essentials',
                'description' => 'Poznaj fundamenty Laravela od routingu po kontenery IoC.',
                'time_limit' => 12,
                'questions' => [
                    [
                        'question_text' => 'Które polecenie tworzy nowy projekt Laravel?',
                        'points' => 1,
                        'order' => 1,
                        'answers' => [
                            ['answer_text' => 'composer create-project laravel/laravel example-app', 'is_correct' => true],
                            ['answer_text' => 'php artisan init', 'is_correct' => false],
                            ['answer_text' => 'laravel run install', 'is_correct' => false],
                        ],
                    ],
                    [
                        'question_text' => 'Która metoda kontenera IoC pozwala wstrzyknąć singleton?',
                        'points' => 2,
                        'order' => 2,
                        'answers' => [
                            ['answer_text' => 'App::singleton()', 'is_correct' => true],
                            ['answer_text' => 'App::bind()', 'is_correct' => false],
                            ['answer_text' => 'App::instance()', 'is_correct' => false],
                        ],
                    ],
                    [
                        'question_text' => 'Jaki middleware odpowiada za ochronę przed CSRF?',
                        'points' => 1,
                        'order' => 3,
                        'answers' => [
                            ['answer_text' => '\\Illuminate\\Foundation\\Http\\Middleware\\VerifyCsrfToken', 'is_correct' => true],
                            ['answer_text' => '\\Illuminate\\Routing\\Middleware\\SubstituteBindings', 'is_correct' => false],
                            ['answer_text' => '\\Illuminate\\Auth\\Middleware\\Authenticate', 'is_correct' => false],
                        ],
                    ],
                    [
                        'question_text' => 'Jaką komendą uruchomisz migracje i seedery jednocześnie?',
                        'points' => 1,
                        'order' => 4,
                        'answers' => [
                            ['answer_text' => 'php artisan migrate --seed', 'is_correct' => true],
                            ['answer_text' => 'php artisan seed --fresh', 'is_correct' => false],
                            ['answer_text' => 'php artisan db:seed --migrate', 'is_correct' => false],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'PHP Fundamentals',
                'description' => 'Rozbudowany quiz z podstaw PHP, typów i standardów PSR.',
                'time_limit' => 15,
                'questions' => [
                    [
                        'question_text' => 'Jaka funkcja zwraca liczbę elementów w tablicy?',
                        'points' => 1,
                        'order' => 1,
                        'answers' => [
                            ['answer_text' => 'count()', 'is_correct' => true],
                            ['answer_text' => 'length()', 'is_correct' => false],
                            ['answer_text' => 'sizeof()', 'is_correct' => false],
                        ],
                    ],
                    [
                        'question_text' => 'Który operator służy do konkatenacji łańcuchów?',
                        'points' => 1,
                        'order' => 2,
                        'answers' => [
                            ['answer_text' => '.', 'is_correct' => true],
                            ['answer_text' => '+', 'is_correct' => false],
                            ['answer_text' => '::', 'is_correct' => false],
                        ],
                    ],
                    [
                        'question_text' => 'Jaka jest domyślna przestrzeń nazw w nowych plikach PHP 8?',
                        'points' => 2,
                        'order' => 3,
                        'answers' => [
                            ['answer_text' => 'Brak, trzeba ją zdefiniować ręcznie', 'is_correct' => true],
                            ['answer_text' => 'App\\', 'is_correct' => false],
                            ['answer_text' => 'Root\\', 'is_correct' => false],
                        ],
                    ],
                    [
                        'question_text' => 'Który standard PSR definiuje autoloading?',
                        'points' => 2,
                        'order' => 4,
                        'answers' => [
                            ['answer_text' => 'PSR-4', 'is_correct' => true],
                            ['answer_text' => 'PSR-7', 'is_correct' => false],
                            ['answer_text' => 'PSR-12', 'is_correct' => false],
                        ],
                    ],
                    [
                        'question_text' => 'Jakiego typu wartości zwraca funkcja array_map?',
                        'points' => 2,
                        'order' => 5,
                        'answers' => [
                            ['answer_text' => 'Nową tablicę z przetworzonymi elementami', 'is_correct' => true],
                            ['answer_text' => 'Iterator', 'is_correct' => false],
                            ['answer_text' => 'Obiekt stdClass', 'is_correct' => false],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'JavaScript Deep Dive',
                'description' => 'Zaawansowany quiz z nowoczesnego JavaScriptu ES2022.',
                'time_limit' => 18,
                'questions' => [
                    [
                        'question_text' => 'Co zwraca metoda Array.prototype.flatMap?',
                        'points' => 2,
                        'order' => 1,
                        'answers' => [
                            ['answer_text' => 'Nową tablicę spłaszczonych wyników mapowania', 'is_correct' => true],
                            ['answer_text' => 'Promise', 'is_correct' => false],
                            ['answer_text' => 'Iterator wartości', 'is_correct' => false],
                        ],
                    ],
                    [
                        'question_text' => 'Jak zdefiniować prywatne pole klasy w JS?',
                        'points' => 2,
                        'order' => 2,
                        'answers' => [
                            ['answer_text' => 'Poprzez prefiks # (np. #count = 0)', 'is_correct' => true],
                            ['answer_text' => 'Poprzez słowo kluczowe private', 'is_correct' => false],
                            ['answer_text' => 'Poprzez symbol Symbol.private', 'is_correct' => false],
                        ],
                    ],
                    [
                        'question_text' => 'Która metoda Promise pozwala czekać na pierwszy spełniony Promise?',
                        'points' => 1,
                        'order' => 3,
                        'answers' => [
                            ['answer_text' => 'Promise.any', 'is_correct' => true],
                            ['answer_text' => 'Promise.all', 'is_correct' => false],
                            ['answer_text' => 'Promise.race', 'is_correct' => false],
                        ],
                    ],
                    [
                        'question_text' => 'Czym jest operator ?? w JavaScript?',
                        'points' => 1,
                        'order' => 4,
                        'answers' => [
                            ['answer_text' => 'Operatorem łączenia z null (nullish coalescing)', 'is_correct' => true],
                            ['answer_text' => 'Operatorem wywołania optional chaining', 'is_correct' => false],
                            ['answer_text' => 'Operatorem bitowym', 'is_correct' => false],
                        ],
                    ],
                    [
                        'question_text' => 'Jakie wartości są traktowane jako falsy w JS?',
                        'points' => 2,
                        'order' => 5,
                        'answers' => [
                            ['answer_text' => '0, "", null, undefined, NaN, false', 'is_correct' => true],
                            ['answer_text' => '0 tylko w typie number', 'is_correct' => false],
                            ['answer_text' => 'Każdy pusty obiekt', 'is_correct' => false],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'DevOps & CI/CD',
                'description' => 'Quiz dla osób, które chcą utrwalić praktyki DevOps i pipeline’y.',
                'time_limit' => 20,
                'questions' => [
                    [
                        'question_text' => 'Jaki jest główny cel Infrastructure as Code?',
                        'points' => 2,
                        'order' => 1,
                        'answers' => [
                            ['answer_text' => 'Automatyzacja i wersjonowanie infrastruktury', 'is_correct' => true],
                            ['answer_text' => 'Manualne wdrożenia na serwerach', 'is_correct' => false],
                            ['answer_text' => 'Zastąpienie monitoringu', 'is_correct' => false],
                        ],
                    ],
                    [
                        'question_text' => 'Które narzędzie służy do deklaratywnego definiowania klastrów Kubernetes?',
                        'points' => 1,
                        'order' => 2,
                        'answers' => [
                            ['answer_text' => 'Helm', 'is_correct' => true],
                            ['answer_text' => 'NPM', 'is_correct' => false],
                            ['answer_text' => 'Composer', 'is_correct' => false],
                        ],
                    ],
                    [
                        'question_text' => 'Co oznacza skrót CI w praktyce DevOps?',
                        'points' => 1,
                        'order' => 3,
                        'answers' => [
                            ['answer_text' => 'Continuous Integration', 'is_correct' => true],
                            ['answer_text' => 'Container Injection', 'is_correct' => false],
                            ['answer_text' => 'Central Instance', 'is_correct' => false],
                        ],
                    ],
                    [
                        'question_text' => 'Jakim poleceniem dockerowym pobierzesz obraz?',
                        'points' => 1,
                        'order' => 4,
                        'answers' => [
                            ['answer_text' => 'docker pull image-name', 'is_correct' => true],
                            ['answer_text' => 'docker fetch image-name', 'is_correct' => false],
                            ['answer_text' => 'docker load image-name', 'is_correct' => false],
                        ],
                    ],
                    [
                        'question_text' => 'Który etap pipeline’u odpowiada za walidację jakości kodu?',
                        'points' => 2,
                        'order' => 5,
                        'answers' => [
                            ['answer_text' => 'Stage QA / testów (lint, unit testy)', 'is_correct' => true],
                            ['answer_text' => 'Stage deploy', 'is_correct' => false],
                            ['answer_text' => 'Stage rollback', 'is_correct' => false],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Open Response Mastery',
                'description' => 'Pięć pytań otwartych wymagających pełnej odpowiedzi tekstowej.',
                'time_limit' => 10,
                'questions' => [
                    [
                        'question_text' => 'Jak nazywa się wzorzec architektoniczny używany w Laravel do rozdzielenia logiki aplikacji?',
                        'points' => 2,
                        'order' => 1,
                        'is_open' => true,
                        'correct_answer_text' => 'Model View Controller',
                        'answers' => [],
                    ],
                    [
                        'question_text' => 'Jakim poleceniem w CLI uruchomisz wszystkie migracje w Laravel?',
                        'points' => 1,
                        'order' => 2,
                        'is_open' => true,
                        'correct_answer_text' => 'php artisan migrate',
                        'answers' => [],
                    ],
                    [
                        'question_text' => 'Podaj nazwę funkcji PHP, która służy do filtrowania elementów tablicy przy użyciu callbacku.',
                        'points' => 2,
                        'order' => 3,
                        'is_open' => true,
                        'correct_answer_text' => 'array_filter',
                        'answers' => [],
                    ],
                    [
                        'question_text' => 'Które rozszerzenie PHP odpowiada za obsługę baz danych poprzez PDO?',
                        'points' => 2,
                        'order' => 4,
                        'is_open' => true,
                        'correct_answer_text' => 'pdo',
                        'answers' => [],
                    ],
                    [
                        'question_text' => 'Jak nazywa się plik konfiguracyjny odpowiedzialny za ustawienia połączenia z bazą danych w Laravel?',
                        'points' => 1,
                        'order' => 5,
                        'is_open' => true,
                        'correct_answer_text' => 'config/database.php',
                        'answers' => [],
                    ],
                ],
            ],
        ];

        foreach ($quizzes as $quizData) {
            $questions = $quizData['questions'] ?? [];
            unset($quizData['questions']);

            $quiz = Quiz::query()->create($quizData);

            foreach ($questions as $questionData) {
                $answers = $questionData['answers'] ?? [];
                unset($questionData['answers']);

                $question = $quiz->questions()->create($questionData);

                foreach ($answers as $answerData) {
                    // Relacje Eloquent dbają o poprawne wypełnienie kluczy obcych.
                    $question->answers()->create($answerData);
                }
            }
        }
    }
}
