<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function index(): View
    {
        $quizzes = Quiz::active()
            ->withCount('questions')
            ->withSum('questions as total_points', 'points')
            ->latest()
            ->get();

        return view('quizzes.index', compact('quizzes'));
    }

    public function show(Request $request, Quiz $quiz): View
    {
        abort_unless($quiz->is_active, 404);

        $questions = $quiz->questions()->with('answers')->get();
        $totalQuestions = $questions->count();

        abort_if($totalQuestions === 0, 404, 'Quiz nie zawiera pytań.');

        $currentStep = max(1, (int)$request->query('step', 1));
        $currentStep = min($currentStep, $totalQuestions);

        $currentQuestion = $questions[$currentStep - 1];
        $answersMap = $this->getStoredAnswers($quiz->id);

        return view('quizzes.show', [
            'quiz' => $quiz,
            'question' => $currentQuestion,
            'currentStep' => $currentStep,
            'totalQuestions' => $totalQuestions,
            'selectedAnswerId' => $answersMap[$currentQuestion->id] ?? null,
        ]);
    }

    public function submit(Request $request, Quiz $quiz): RedirectResponse
    {
        abort_unless($quiz->is_active, 404);

        $validated = $request->validate([
            'question_id' => ['required', 'integer'],
            'answer_id' => ['required', 'integer'],
            'step' => ['required', 'integer', 'min:1'],
            'is_final' => ['nullable', 'boolean'],
        ]);

        /** @var Question $question */
        $question = $quiz->questions()->findOrFail($validated['question_id']);

        /** @var Answer|null $answer */
        $answer = $question->answers()->whereKey($validated['answer_id'])->first();

        abort_if(is_null($answer), 422, 'Wybrana odpowiedź jest nieprawidłowa.');

        $answersMap = $this->getStoredAnswers($quiz->id);
        $answersMap[$question->id] = $answer->id;
        $this->storeAnswers($quiz->id, $answersMap);

        $totalQuestions = $quiz->questions()->count();
        $currentStep = (int)$validated['step'];
        $isFinal = (bool)($validated['is_final'] ?? false) || $currentStep >= $totalQuestions;

        if ($isFinal) {
            $result = $this->buildResults($quiz, $answersMap);
            $this->storeResult($quiz->id, $result);
            $this->clearAnswers($quiz->id);

            return redirect()->route('quizzes.results', $quiz);
        }

        return redirect()->route('quizzes.show', [$quiz, 'step' => $currentStep + 1]);
    }

    public function results(Quiz $quiz): View
    {
        $result = $this->getStoredResult($quiz->id);

        abort_if(is_null($result), 404, 'Nie znaleziono wyników dla tego quizu.');

        return view('quizzes.results', [
            'quiz' => $quiz,
            'result' => $result,
        ]);
    }

    protected function answersSessionKey(int $quizId): string
    {
        return "quiz_answers.$quizId";
    }

    protected function resultSessionKey(int $quizId): string
    {
        return "quiz_results.$quizId";
    }

    protected function getStoredAnswers(int $quizId): array
    {
        return session()->get($this->answersSessionKey($quizId), []);
    }

    protected function storeAnswers(int $quizId, array $answers): void
    {
        session()->put($this->answersSessionKey($quizId), $answers);
    }

    protected function clearAnswers(int $quizId): void
    {
        session()->forget($this->answersSessionKey($quizId));
    }

    protected function storeResult(int $quizId, array $result): void
    {
        session()->put($this->resultSessionKey($quizId), $result);
    }

    protected function getStoredResult(int $quizId): ?array
    {
        return session()->get($this->resultSessionKey($quizId));
    }

    protected function buildResults(Quiz $quiz, array $answersMap): array
    {
        $quiz->loadMissing('questions.answers');

        $details = [];
        $correctCount = 0;

        foreach ($quiz->questions as $question) {
            $selectedId = $answersMap[$question->id] ?? null;
            $selectedAnswer = $question->answers->firstWhere('id', $selectedId);
            $correctAnswer = $question->answers->firstWhere('is_correct', true);
            $isCorrect = $selectedAnswer?->is_correct ?? false;

            if ($isCorrect) {
                $correctCount++;
            }

            $details[] = [
                'question_text' => $question->question_text,
                'points' => $question->points,
                'selected_answer' => $selectedAnswer?->answer_text,
                'correct_answer' => $correctAnswer?->answer_text,
                'is_correct' => $isCorrect,
            ];
        }

        $totalQuestions = max(1, $quiz->questions->count());

        return [
            'total_questions' => $quiz->questions->count(),
            'total_correct' => $correctCount,
            'percentage' => round(($correctCount / $totalQuestions) * 100),
            'details' => $details,
        ];
    }
}
