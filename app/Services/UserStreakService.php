<?php

namespace App\Services;

use App\Models\UserStreak;
use Carbon\Carbon;

class UserStreakService
{
    public function getStreak(int $userId) : array | UserStreak
    {
        $streak = UserStreak::select([
            'streak',
            'day_score',
            'week_score',
            'total_score',
        ])
            ->where('user_id', $userId)
            ->first();
        if(!$streak) {
            return [
                'streak' => 0,
                'day_score' => 0,
                'week_score' => 0,
                'total_score' => 0,
            ];
        }
        return $streak;
    }

    public function update(int $userId, int $score = 0) : void
    {

        // Get existing streak data or create new one
        $userStreak = UserStreak::where('user_id', $userId)->first();
        if(!$userStreak) {
            $userStreakData = [
                'user_id' => $userId,
                'streak' => 0,
                'last_quiz_date' => now()->subDay(),
            ];
            UserStreak::insert($userStreakData);
            $userStreak = UserStreak::where('user_id', $userId)->first();
        }

        // Update streak data
        $userStreak->day_score += $score;
        $userStreak->week_score += $score;
        $userStreak->total_score += $score;
        if($userStreak->day_score > $userStreak->day_score_record) {
            $userStreak->day_score_record = $userStreak->day_score;
        }
        if($userStreak->week_score > $userStreak->week_score_record) {
            $userStreak->week_score_record = $userStreak->week_score;
        }


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
