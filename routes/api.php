<?php

use App\Models\Category;
use App\Models\Question;
use App\Models\User;
use App\Models\UserQuestionResponse;
use App\Services\GetQuizService;
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


Route::prefix('get-quiz')->group(function() {

    function getQuiz(int $mainCategory = -1, int $subCategory = -1) {
        $quizData = (new GetQuizService())->getQuiz(1, $mainCategory, $subCategory);
        return response()->json($quizData);
    }
    Route::get('/', function(Request $request) {
        return getQuiz();
    });

    Route::get('/{mainCategory}', function(Request $request, int $mainCategory) {
        return getQuiz($mainCategory);
    });

    Route::get('/{mainCategory}/{subCategory}', function(Request $request, int $mainCategory, int $subCategory) {
        return getQuiz($mainCategory, $subCategory);
    });

});

// TODO this can be moved to the web route (this whole file can be actually) under a middleware
Route::post("/questions/flag-question", function(Request $request) {
    $request->validate([
        'questionId' => 'required|integer|exists:questions,id',
        'reason' => 'required|string|max:255'
    ]);
    Question::where('id', $request->questionId)
        ->update([
            'flagged' => true,
            'flagged_reason' => $request->reason
        ]);
});


Route::post('answer-quiz', [\App\Http\Controllers\AnswerQuizController::class, 'store']);
