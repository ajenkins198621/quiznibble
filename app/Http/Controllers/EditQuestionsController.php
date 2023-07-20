<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\CategoryTag;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class EditQuestionsController extends Controller
{

    public function getQuestions() {
        return Inertia::render('EditQuestions/EditQuestions', (new \App\Services\GetCategoriesService())->get());
    }

    public function addQuestion(Request $request) : JsonResponse {

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


    public function addCategoryOrTag (Request $request) : JsonResponse {
        $validator = Validator::make($request->all(), [
            'type' => 'in:primaryCategory,subCategory,tag',
            'parentId' => 'nullable|numeric',
            'name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid request',
                'errors' => $validator->errors()
            ], 400);
        }

        $addType = '';
        if($request->type == 'primaryCategory' || $request->type == 'subCategory') {
            $category = new \App\Models\Category();
            if(isset($request->parentId) && is_numeric($request->parentId) && $request->parentId > 0) {
                $category->parent_id = $request->parentId;
            }
            $category->category_name = $request->name;
            $category->save();
            $addType = 'category';
        } elseif ($request->type == 'tag') {
            $tag = new \App\Models\Tag();
            $tag->tag_name = $request->name;
            $tag->save();
            $addType = 'tag';

            CategoryTag::insert([
                'category_id' => $request->parentId,
                'tag_id' => $tag->id,
            ]);
        }

        $categoriesArray = (new \App\Services\GetCategoriesService())->get();
        return response()->json(array_merge([
            'message' => "Successfully added {$addType}!"
        ], $categoriesArray), 201);
    }

}
