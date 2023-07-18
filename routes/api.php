<?php

use App\Models\Category;
use App\Models\User;
use App\Models\UserQuestionResponse;
use App\Services\UserStreakService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')
    ->get('/user', function (Request $request) {
        return $request->user();
    });

Route::get('/get-categories', function(Request $request) {
    $categories = Category::select([
        'id',
        'category_name',
        'parent_id'
    ])
        ->whereNull('parent_id')
        ->with([
            'subCategories:id,category_name,parent_id',
            'subCategories.tags:id,tag_name',
            'tags:id,tag_name',
        ])
        ->get();
    return response()->json($categories);
});


Route::get('get-quiz', function(Request $request) {


    function getQuestionIds(User $user) {
        // Step 1: Get 4 questions user hasn't answered before
        $newQuestions = \App\Models\Question::select('id')->whereNotIn('id', function($query) use ($user) {
            $query->select('question_id')
                ->from('user_question_responses')
                ->where('user_id', $user->id);
        })->inRandomOrder()->limit(4)->get();

        // Get the IDs of the 4 worst-answered questions
        $worstQuestionIds = UserQuestionResponse::where('user_id', $user->id)
            ->select('question_id')
            ->selectRaw('(correct_count / attempt_count) as success_rate')
            ->orderBy('success_rate', 'asc')
            ->limit(4)
            ->pluck('question_id');

        // Get the Question models for these IDs
        $poorlyAnsweredQuestions = \App\Models\Question::whereIn('id', $worstQuestionIds)->get();


        // Step 3: Get the remaining questions at random
        $NUM_QUESTIONS = 12;
        $randomQuestions = \App\Models\Question::select('id')
            ->whereNotIn('id', $newQuestions->pluck('id')
                ->concat($poorlyAnsweredQuestions->pluck('id'))
            )
            ->inRandomOrder()
            ->limit($NUM_QUESTIONS - $newQuestions->count() - $poorlyAnsweredQuestions->count())
            ->get();

        // Combine all questions
        $quizQuestions = $poorlyAnsweredQuestions->concat($newQuestions)->concat($randomQuestions);

        return $quizQuestions->pluck('id');
    }

    $userId = 1; // TODO move to actual user

    $questions = \App\Models\Question::select([
        'id',
        'question',
        'category_id',
        'question_type_id',
        'hint',
    ])
    ->with([
        'answers:id,question_id,answer,is_correct',
        'category:id,parent_id,category_name',
        'category.parent:id,parent_id,category_name',
        'questionType:id,question_type',
        'tags:id,tag_name',
    ])
    ->whereIn('id', getQuestionIds(User::find($userId)))
    ->get();
    return response()->json([
        'questions' => $questions,
        'userStreak' => (new UserStreakService($userId))->getStreak(),
    ]);
});

Route::post('answer-quiz', function(Request $request) {
    $data = $request->validate([
        'answers' => 'required|array',
        'answers.*.question_id' => 'required|exists:questions,id',
        'answers.*.is_correct' => 'required|boolean',
    ]);

    $userId = 1; // TODO move to actual user

    foreach ($data['answers'] as $answer) {
        $existing = \App\Models\UserQuestionResponse::where([
            'user_id' => $userId,
            'question_id' => $answer['question_id']
        ])->first();
        if($existing) {
            $newCorrectCount = $answer['is_correct'] ? $existing->correct_count + 1 : $existing->correct_count;
            $newIncorrectCount = $answer['is_correct'] ? $existing->incorrect_count : $existing->incorrect_count + 1;
            $newAttemptCount = $existing->attempt_count + 1;
            $existing->update([
                'correct_count' => $newCorrectCount,
                'incorrect_count' => $newIncorrectCount,
                'attempt_count' => $newAttemptCount,
            ]);
        } else {
            \App\Models\UserQuestionResponse::insert([
                'user_id' => $userId,
                'question_id' => $answer['question_id'],
                'correct_count' => $answer['is_correct'] ? 1 : 0,
                'incorrect_count' => $answer['is_correct'] ? 0 : 1,
                'attempt_count' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

    }

    // TODO Add this to a listener at some point with an event tied to it
    $userStreakService = new UserStreakService($userId);
    $userStreakService->update();

    // Return a response indicating success
    return response()->json([
        'message' => 'Responses saved successfully.',
        'userStreak' => $userStreakService->getStreak(),
    ], 201);


});

Route::get('/test-update-streak', function(Request $request) {
    $userId = 1; // TODO move to actual user
    $userStreakService = new UserStreakService($userId);
    $userStreakService->update();
    return response()->json([
        'message' => 'Streak updated successfully.',
        'userStreak' => $userStreakService->getStreak(),
    ], 201);
});
