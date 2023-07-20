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

        $userId = auth()->id();

        $this->userQuestionResponseService->store($userId, $data['answers']);

        $this->userStreakService->update($userId);

        return response()->json([
            'message' => 'Responses saved successfully.',
            'userStreak' => $this->userStreakService->getStreak($userId),
        ], 201);
    }
}
