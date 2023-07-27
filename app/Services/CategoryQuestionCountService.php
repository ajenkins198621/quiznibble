<?php

namespace App\Services;

use App\Models\Category;
use App\Models\CategoryQuestionCount;
use App\Models\Question;

class CategoryQuestionCountService {

    public function updateAll() {
        $categoryIds = Question::select([
            'category_id'
        ])
        ->get()
        ->pluck('category_id')
        ->toArray();

        $counts = [];
        foreach($categoryIds as $categoryId) {
            if(!isset($counts[$categoryId])) {
                $counts[$categoryId] = 0;
            }
            $counts[$categoryId]++;
        }

        $insert = [];
        foreach ($counts as $category_id => $count) {
            $insert[] = [
                'category_id' => $category_id,
                'question_count' => $count,
            ];
        }
        CategoryQuestionCount::truncate();
        CategoryQuestionCount::insert($insert);
    }
}
