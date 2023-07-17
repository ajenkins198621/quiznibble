<?php

namespace App\Services;

use App\Models\UserStreak;
use Carbon\Carbon;

class UserStreakService
{
    private int $userId;
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function getStreak() : int
    {
        $streak = UserStreak::select('streak')
            ->where('user_id', $this->userId)
            ->first();
        if(!$streak) {
            return 0;
        }
        return $streak->streak;
    }

    public function update() : void
    {
        $userStreak = UserStreak::firstOrCreate(
            ['user_id' => $this->userId],
            ['streak' => 0, 'last_quiz_date' => now()->subDay()]
        );

        $lastQuizDate = Carbon::parse($userStreak->last_quiz_date);

        if ($lastQuizDate->isYesterday()) {
            $userStreak->streak++;
        } elseif (!$lastQuizDate->isToday()) {
            $userStreak->streak = 1;
        }

        $userStreak->last_quiz_date = now();
        $userStreak->save();
    }
}
