<?php

use App\Models\Category;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
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
    ->inRandomOrder() // TODO This needs to be an algorithm
    ->limit(3) // TODO Make this an option
    ->get();
    return response()->json($questions);
});

Route::post('answer-quiz', function(Request $request) {
    $data = $request->validate([
        'answers' => 'required|array',
        'answers.*.question_id' => 'required|exists:questions,id',
        'answers.*.is_correct' => 'required|boolean',
    ]);

    foreach ($data['answers'] as $answer) {
        $existing = \App\Models\UserQuestionResponse::where([
            'user_id' => 1, // TODO move to actual user
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
            \App\Models\UserQuestionResponse::create([
                'user_id' => 1, // TODO move to actual user
                'question_id' => $answer['question_id'],
                'correct_count' => $answer['is_correct'] ? 1 : 0,
                'incorrect_count' => $answer['is_correct'] ? 0 : 1,
                'attempt_count' => 1,
            ]);
        }

    }

    // Return a response indicating success
    return response()->json(['message' => 'Responses saved successfully.', 201]);


});
