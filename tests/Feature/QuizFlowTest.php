<?php

namespace Tests\Feature;

use App\Models\Quiz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Strona główna powinna ładować się poprawnie i zawierać nazwę aplikacji.
     */
    public function test_home_page_displays_quiz_count(): void
    {
        $this->seed();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('QuizApp');
    }

    /**
     * Widok listy quizów musi wyświetlać przycisk CTA, aby rozpocząć quiz.
     */
    public function test_quizzes_index_lists_quizzes(): void
    {
        $this->seed();

        $this->get(route('quizzes.index'))
            ->assertOk()
            ->assertSee('Rozpocznij');
    }

    /**
     * Pełny scenariusz: odpowiedz na pytanie i sprawdź, czy wyniki są dostępne.
     */
    public function test_quiz_flow_generates_results(): void
    {
        $this->seed();

        $quiz = Quiz::first();
        $question = $quiz->questions()->with('answers')->first();
        $correctAnswer = $question->answers->firstWhere('is_correct', true);

        $this->post(route('quizzes.submit', $quiz), [
            'question_id' => $question->id,
            'answer_id' => $correctAnswer->id,
            'step' => 1,
            'is_final' => 1,
        ])->assertRedirect(route('quizzes.results', $quiz));

        $this->get(route('quizzes.results', $quiz))
            ->assertOk()
            ->assertSee('Wyniki');
    }
}

