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
    ->limit(15) // TODO Make this an option
    ->get();
    return response()->json($questions);
});
