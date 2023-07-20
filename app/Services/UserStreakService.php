<?php

namespace App\Services;

use App\Models\UserStreak;
use Carbon\Carbon;

class UserStreakService
{
    public function getStreak(int $userId) : int
    {
        $streak = UserStreak::select('streak')
            ->where('user_id', $userId)
            ->first();
        if(!$streak) {
            return 0;
        }
        return $streak->streak;
    }

    public function update(int $userId) : void
    {
        $userStreak = UserStreak::firstOrCreate(
            ['user_id' => $userId],
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
