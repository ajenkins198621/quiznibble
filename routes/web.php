<?php

use App\Http\Controllers\EditQuestionsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ViewDashboardController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Answer;
use App\Models\Category;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::prefix('dashboard')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [ViewDashboardController::class, 'viewDashboard'])->name('dashboard');
    Route::get('/quiz/{mainCategoryId}', [ViewDashboardController::class, 'viewDashboard']);
    Route::get('/quiz/{mainCategoryId}/{subCategoryId}', [ViewDashboardController::class, 'viewDashboard']);

    Route::prefix('questions')->group(function() {
        Route::get('/edit-questions', [EditQuestionsController::class, 'getQuestions'])->name('dashboard.questions.edit-questions');
        Route::post('/add-question', [EditQuestionsController::class, 'addQuestion'])->name('dashboard.questions.add-question');
        Route::post('/add-category-or-tag', [EditQuestionsController::class, 'addCategoryOrTag'])->name('dashboard.questions.add-category-or-tag');
    });

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



Route::get('/view-questions', function () {
    $questions = Question::select([
        'id',
        'question',
        'category_id',
        'question_type_id',
        'hint',
        'created_at',
    ])
    ->with([
        'answers:id,question_id',
        'category:id,parent_id,category_name',
        'category.parent:id,parent_id,category_name',
        'questionType:id,question_type',
        'tags:id,tag_name',
    ])->get();
    return view('view-questions', [
        'questions' => $questions,
    ]);
})->name('view-questions');

Route::get('/quiz-ideas', function () {
    $quizIdeas = \App\Models\QuizIdea::get();
    return view('quiz-ideas', [
        'quizIdeas' => $quizIdeas,
    ]);
})->name('quiz-ideas');

Route::post('create-quiz-idea', function (Request $request) {
    $validator = Validator::make($request->all(), [
        'idea' => 'required|string',
    ]);
    if ($validator->fails()) {
        dd($validator->errors());
    }

    $quizIdea = new \App\Models\QuizIdea();
    $quizIdea->idea = $request->idea;
    $quizIdea->save();

    return redirect('/quiz-ideas')->with('status', 'Quiz idea saved successfully!');
});

Route::delete('delete-quiz-idea', function (Request $request) {
    $validator = Validator::make($request->all(), [
        'quiz_idea_id' => 'required|exists:quiz_ideas,id',
    ]);
    if ($validator->fails()) {
        dd($validator->errors());
    }

    $quizIdea = \App\Models\QuizIdea::find($request->quiz_idea_id);
    $quizIdea->delete();

    return redirect('/quiz-ideas')->with('status', 'Quiz idea deleted successfully!');
})->name('delete-quiz-idea');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
