<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class QuizController extends Controller
{
    /**
     * Lista wszystkich aktywnych quizów wraz z liczbą pytań i sumą punktów.
     */
    public function index(): View
    {
        $quizzes = Quiz::active()
            ->withCount('questions')
            ->withSum('questions as total_points', 'points')
            ->latest()
            ->get();

        return view('quizzes.index', compact('quizzes'));
    }

    /**
     * Prezentuje pojedynczy quiz krok po kroku.
     * Korzystamy z query stringa ?step= aby móc wrócić do poprzedniego pytania.
     */
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
        $storedAnswer = $this->resolveAnswerPayload($answersMap, $currentQuestion->id);

        return view('quizzes.show', [
            'quiz' => $quiz,
            'question' => $currentQuestion,
            'currentStep' => $currentStep,
            'totalQuestions' => $totalQuestions,
            'selectedAnswerId' => $storedAnswer['answer_id'],
            'typedAnswer' => $storedAnswer['answer_text'],
        ]);
    }

    /**
     * Zapisuje odpowiedź użytkownika i decyduje, czy przejść dalej, czy pokazać wynik.
     */
    public function submit(Request $request, Quiz $quiz): RedirectResponse
    {
        abort_unless($quiz->is_active, 404);

        $baseData = $request->validate([
            'question_id' => ['required', 'integer'],
            'step' => ['required', 'integer', 'min:1'],
            'is_final' => ['nullable', 'boolean'],
        ]);

        /** @var Question $question */
        $question = $quiz->questions()->findOrFail($baseData['question_id']);

        if ($question->is_open) {
            $input = $request->validate([
                'answer_text' => ['required', 'string', 'max:2000'],
            ]);

            $answerPayload = [
                'answer_id' => null,
                'answer_text' => $input['answer_text'],
            ];
        } else {
            $input = $request->validate([
                'answer_id' => ['required', 'integer'],
            ]);

            /** @var Answer|null $answer */
            $answer = $question->answers()->whereKey($input['answer_id'])->first();
            abort_if(is_null($answer), 422, 'Wybrana odpowiedź jest nieprawidłowa.');

            $answerPayload = [
                'answer_id' => $answer->id,
                'answer_text' => null,
            ];
        }

        $answersMap = $this->getStoredAnswers($quiz->id);
        $answersMap[$question->id] = $answerPayload;
        $this->storeAnswers($quiz->id, $answersMap);

        $totalQuestions = $quiz->questions()->count();
        $currentStep = (int)$baseData['step'];
        $isFinal = (bool)($baseData['is_final'] ?? false) || $currentStep >= $totalQuestions;

        if ($isFinal) {
            $result = $this->buildResults($quiz, $answersMap);
            $this->storeResult($quiz->id, $result);
            $this->clearAnswers($quiz->id);

            return redirect()->route('quizzes.results', $quiz);
        }

        return redirect()->route('quizzes.show', [$quiz, 'step' => $currentStep + 1]);
    }

    /**
     * Renderuje ekran wyników bazując na danych trzymanych chwilowo w sesji.
     */
    public function results(Quiz $quiz): View
    {
        $result = $this->getStoredResult($quiz->id);

        abort_if(is_null($result), 404, 'Nie znaleziono wyników dla tego quizu.');

        return view('quizzes.results', [
            'quiz' => $quiz,
            'result' => $result,
        ]);
    }

    /**
     * Unikatowy klucz sesji trzymający odpowiedzi w trakcie bieżącego quizu.
     */
    protected function answersSessionKey(int $quizId): string
    {
        return "quiz_answers.$quizId";
    }

    /**
     * Nazwa klucza sesji przechowującego gotowy wynik.
     */
    protected function resultSessionKey(int $quizId): string
    {
        return "quiz_results.$quizId";
    }

    /**
     * Odczytuje zapisane wcześniej odpowiedzi (mapa question_id => answer_id).
     */
    protected function getStoredAnswers(int $quizId): array
    {
        return session()->get($this->answersSessionKey($quizId), []);
    }

    /**
     * Nadpisuje odpowiedzi w sesji – dzięki temu możemy budować quiz krokowo.
     */
    protected function storeAnswers(int $quizId, array $answers): void
    {
        session()->put($this->answersSessionKey($quizId), $answers);
    }

    /**
     * Czyści odpowiedzi po zakończeniu quizu, żeby nowa sesja startowała na czysto.
     */
    protected function clearAnswers(int $quizId): void
    {
        session()->forget($this->answersSessionKey($quizId));
    }

    /**
     * Odkłada wynik końcowy do sesji – wyświetlamy go tuż po przekierowaniu.
     */
    protected function storeResult(int $quizId, array $result): void
    {
        session()->put($this->resultSessionKey($quizId), $result);
    }

    /**
     * Pobiera ostatni wynik z sesji.
     */
    protected function getStoredResult(int $quizId): ?array
    {
        return session()->get($this->resultSessionKey($quizId));
    }

    /**
     * Składa tablicę wyników w czytelnej strukturze dla widoku.
     */
    protected function buildResults(Quiz $quiz, array $answersMap): array
    {
        $quiz->loadMissing('questions.answers'); // eager load, by uniknąć N+1

        $details = [];
        $correctCount = 0;

        foreach ($quiz->questions as $question) {
            $payload = $this->resolveAnswerPayload($answersMap, $question->id);

            if ($question->is_open) {
                $isCorrect = $this->isOpenAnswerCorrect($payload['answer_text'], $question->correct_answer_text);
                $selectedAnswerText = $payload['answer_text'];
                $correctAnswerText = $question->correct_answer_text;
            } else {
                $selectedAnswer = $question->answers->firstWhere('id', $payload['answer_id']);
                $correctAnswer = $question->answers->firstWhere('is_correct', true);
                $isCorrect = $selectedAnswer?->is_correct ?? false;
                $selectedAnswerText = $selectedAnswer?->answer_text;
                $correctAnswerText = $correctAnswer?->answer_text;
            }

            if ($isCorrect) {
                $correctCount++;
            }

            $details[] = [
                'question_text' => $question->question_text,
                'points' => $question->points,
                'selected_answer' => $selectedAnswerText,
                'correct_answer' => $correctAnswerText,
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

    /**
     * Standaryzuje strukturę odpowiedzi z sesji (obsługuje starszy format int).
     */
    protected function resolveAnswerPayload(array $answersMap, int $questionId): array
    {
        $defaults = ['answer_id' => null, 'answer_text' => null];
        $raw = $answersMap[$questionId] ?? null;

        if (is_array($raw)) {
            return array_merge($defaults, $raw);
        }

        if ($raw === null) {
            return $defaults;
        }

        return ['answer_id' => $raw, 'answer_text' => null];
    }

    /**
     * Porównuje odpowiedź otwartą w sposób nieczuły na wielkość liter i białe znaki.
     */
    protected function isOpenAnswerCorrect(?string $userInput, ?string $expected): bool
    {
        if ($expected === null) {
            return false;
        }

        return $this->normalizeAnswerText($userInput) === $this->normalizeAnswerText($expected);
    }

    protected function normalizeAnswerText(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return Str::lower(trim($value));
    }
}
