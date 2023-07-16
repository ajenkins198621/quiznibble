<?php

use App\Http\Controllers\ProfileController;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return 'As of now, this application is only an API. Please use the API endpoints to interact with the application. You can <a href="/create-question">Add a question</a> here.';
});

Route::get('/create-question', function () {
    return view('create-questions', [
        'categories' => \App\Models\Category::get(),
        'sub_categories' => \App\Models\Category::whereNotNull('parent_id')->get(),
        'tags' => \App\Models\Tag::get(),
    ]);
});

Route::post('/create-questions', function (Request $request) {
    $validator = Validator::make($request->all(), [
        'category_id' => 'required|exists:categories,id',
        'subcategory_id' => 'required|exists:categories,id',
        'tag_id' => 'required|exists:tags,id',
        'json' => 'required|json',
    ]);
    if ($validator->fails()) {
        dd($validator->errors());
    }

    $data = json_decode($request->json, true);

    // Loop through each question to validate the structure
    foreach ($data as $questionData) {
        $validator = Validator::make($questionData, [
            'question' => 'required|string',
            'options' => 'required|array',
            'options.*.option' => 'required|string',
            'options.*.isAnswer' => 'required|boolean',
            'hint' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Insert question and options into database
        $question = new Question();
        $question->question = $questionData['question'];
        $question->question_type_id = 1;
        $question->category_id = $request->subcategory_id;
        $question->hint = $questionData['hint'];
        $question->save();

        $questionTag = new \App\Models\QuestionTag();
        $questionTag->question_id = $question->id;
        $questionTag->tag_id = $request->tag_id;
        $questionTag->save();

        foreach ($questionData['options'] as $answerData) {
            $answer = new Answer();
            $answer->question_id = $question->id;
            $answer->answer = $answerData['option'];
            $answer->is_correct = $answerData['isAnswer'] == "true" ? 1 : 0;
            $answer->question_id = $question->id;
            $answer->save();
        }
    }

    return redirect('/create-question')->with('status', 'Quiz saved successfully!');
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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
