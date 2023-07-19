<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class EditQuestionsController extends Controller
{

    public function getQuestions() {
        return Inertia::render('EditQuestions/EditQuestions', [
            'categories' => \App\Models\Category::whereNull('parent_id')->get(),
            'sub_categories' => \App\Models\Category::whereNotNull('parent_id')
                ->with('tags')
                ->get(),
        ]);
    }

    public function updateQuestion(Request $request) {

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:categories,id',
            'tag_id' => 'required|exists:tags,id',
            'mediaUrl' => 'nullable|string',
            'code' => 'nullable|string',
            'json' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid request',
                'errors' => $validator->errors()
            ], 400);
        }

        $questionJson = json_decode($request->json, true);

        // Loop through each question to validate the structure
        $questionCount = 0;
        foreach ($questionJson as $questionData) {
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

            $questionCount++;

            foreach ($questionData['options'] as $answerData) {
                $answer = new Answer();
                $answer->question_id = $question->id;
                $answer->answer = $answerData['option'];
                $answer->is_correct = $answerData['isAnswer'] == "true" ? 1 : 0;
                $answer->question_id = $question->id;
                $answer->save();
            }
        }

        return response()->json([
            'message' => "Successfully added {$questionCount} questions!"
        ], 201);



    }

}
