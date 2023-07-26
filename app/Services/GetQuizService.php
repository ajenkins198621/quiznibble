<?php

namespace App\Services;

use App\Models\Category;
use App\Models\User;
use App\Models\UserQuestionResponse;

class GetQuizService {


    public function getQuiz(int $userId, int $mainCategoryId = -1, int $subCategoryId = -1) : array
    {

        $questions = \App\Models\Question::select([
            'id',
            'question',
            'category_id',
            'question_type_id',
            'hint',
            'detail_url',
        ])
        ->with([
            'answers:id,question_id,answer,is_correct',
            'category:id,parent_id,category_name',
            'category.parent:id,parent_id,category_name',
            'questionType:id,question_type',
            'tags:id,tag_name',
        ])
        ->whereIn('id', $this->_getQuestionIds(User::find($userId), $mainCategoryId, $subCategoryId))
        ->get();
        return [
            'questions' => $questions,
            'userStreak' => (new UserStreakService())->getStreak($userId),
        ];
    }

    private function _getQuestionIds(User $user, int $mainCategoryId, int $subCategoryId) : array
    {

        // For debugging can change this!
        $NUM_QUESTIONS_PER_SECTION = 4;

        $categoryIdToUse = 1;
        if($subCategoryId != -1) {
            $categoryIdToUse = $subCategoryId;
        } else if($mainCategoryId != -1) {
            $categoryIdToUse = $mainCategoryId;
        }

        // Get Category Ids
        $categoryIds = collect(Category::select('id')
            ->where('id', $categoryIdToUse)
            ->orWhere('parent_id', $categoryIdToUse)
            ->get())
            ->pluck('id');

        // Step 1: Get 4 questions user hasn't answered before
        $newQuestions = \App\Models\Question::select('id')
            ->whereNotIn('id', function($query) use ($user) {
                $query->select('question_id')
                    ->from('user_question_responses')
                    ->where('user_id', $user->id);
                })
            ->whereIn('category_id',  $categoryIds)
            ->inRandomOrder()
            ->limit($NUM_QUESTIONS_PER_SECTION)
            ->get();

        // Get the IDs of the 4 worst-answered questions
        $worstQuestionIds = UserQuestionResponse::where('user_id', $user->id)
            ->select('question_id')
            ->selectRaw('(correct_count / attempt_count) as success_rate')
            ->orderBy('success_rate', 'asc')
            ->limit($NUM_QUESTIONS_PER_SECTION)
            ->pluck('question_id');

        // Get the Question models for these IDs
        $poorlyAnsweredQuestions = \App\Models\Question::whereIn('id', $worstQuestionIds)
            ->whereIn('category_id',  $categoryIds)
            ->get();

        // Step 3: Get the remaining questions at random
        $randomQuestions = \App\Models\Question::select('id')
            ->whereNotIn('id', $newQuestions->pluck('id')
                ->concat($poorlyAnsweredQuestions->pluck('id'))
            )
            ->whereIn('category_id',  $categoryIds)
            ->inRandomOrder()
            ->limit(($NUM_QUESTIONS_PER_SECTION * 3) - $newQuestions->count() - $poorlyAnsweredQuestions->count())
            ->get();

        // Combine all questions
        $quizQuestions = $poorlyAnsweredQuestions->concat($newQuestions)->concat($randomQuestions);

        return $quizQuestions->pluck('id')->toArray();
    }


}
