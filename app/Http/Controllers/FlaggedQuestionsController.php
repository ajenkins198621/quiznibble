<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FlaggedQuestionsController extends Controller
{

    public function getFlagged(Request $request)
    {
        $questions = Question::select([
            'id',
            'question',
            'flagged',
            'flagged_reason',
            'active',
        ])
            ->where('flagged', true)
            ->get();

        return Inertia::render('FlaggedQuestions/FlaggedQuestions', [
            'questions' => $questions,
        ]);
    }
}
