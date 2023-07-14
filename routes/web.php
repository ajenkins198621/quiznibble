<?php

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
    return 'As of now, this application is only an API. Please use the API endpoints to interact with the application. You can <a href="/create-questions">Add a question</a> here.';
});

Route::get('/create-questions', function () {
    return view('create-questions', [
        'categories' => \App\Models\Category::get(),
        'sub_categories' => \App\Models\Category::whereNotNull('parent_id')->get(),
        'tags' => \App\Models\Tag::get(),
    ]);
})->name('create-questions');

Route::post('/create-questions', function(Request $request) {
    $request->validate([
        'category1' => 'required|exists:categories,id',
        'subcategory' => 'required|exists:categories,id',
        'tag' => 'required',
        'json' => 'required|json',
    ]);
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
            $question->category_id = $request->category1;
            $question->hint = $questionData['hint'];
            $question->save();

            $questionTag = new \App\Models\QuestionTag();
            $questionTag->question_id = $question->id;
            $questionTag->tag_id = $request->tag;
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

        return redirect()->route('create-questions')->with('status', 'Quiz saved successfully!');

});
