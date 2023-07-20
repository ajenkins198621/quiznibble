<?php

namespace App\Services;

use App\Models\UserQuestionResponse;

class UserQuestionResponseService
{
    public function store($userId, array $answers)
    {
        foreach ($answers as $answer) {
            $existing = UserQuestionResponse::where([
                'user_id' => $userId,
                'question_id' => $answer['question_id']
            ])->first();

            if ($existing) {
                $existing->increment('correct_count', $answer['is_correct'] ? 1 : 0);
                $existing->increment('incorrect_count', $answer['is_correct'] ? 0 : 1);
                $existing->increment('attempt_count');
            } else {
                UserQuestionResponse::insert([
                    'user_id' => $userId,
                    'question_id' => $answer['question_id'],
                    'correct_count' => $answer['is_correct'] ? 1 : 0,
                    'incorrect_count' => $answer['is_correct'] ? 0 : 1,
                    'attempt_count' => 1,
                ]);
            }
        }
    }
}
