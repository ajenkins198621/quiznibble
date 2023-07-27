<?php

namespace App\Http\Controllers;

use App\Services\UserQuestionResponseService;
use App\Services\UserStreakService;
use Illuminate\Http\Request;

class AnswerQuizController extends Controller
{
    protected $userQuestionResponseService;
    protected $userStreakService;

    public function __construct(
        UserQuestionResponseService $userQuestionResponseService,
        UserStreakService $userStreakService
    ) {
        $this->userQuestionResponseService = $userQuestionResponseService;
        $this->userStreakService = $userStreakService;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.is_correct' => 'required|boolean',
        ]);

        // $userId = auth()->id();
        $userId = 1; // TODO: Get the authenticated user ID

        $this->userQuestionResponseService->store($userId, $data['answers']);

        $this->userStreakService->update($userId, $this->_getScore($data['answers']));

        return response()->json([
            'message' => 'Responses saved successfully.',
            'userStreak' => $this->userStreakService->getStreak($userId),
        ], 201);
    }

    private function _getScore($answers) {
        $total_count = 0;
        $correct_count = 0;
        foreach ($answers as $answer) {
            $total_count++;
            if ($answer['is_correct']) {
                $correct_count++;
            }
        }
        // Return as a single digit number which is the percentage of correct answers rounded down
        return floor($correct_count / $total_count * 10);
    }
}
