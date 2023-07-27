<?php

namespace App\Services;

use App\Models\Category;
use App\Models\CategoryQuestionCount;
use App\Models\Question;

class CategoryQuestionCountService {

    public function updateAll() {
        $categories = Question::select([
            'category_id',

        ])
            ->with([
                'category:id,parent_id',
            ])
            ->get()
            ->toArray();

        $counts = [];
        foreach($categories as $category) {
            if(!isset($counts[$category['category_id']])) {
                $counts[$category['category_id']] = 0;
            }
            if(isset($category['category']['parent_id']) && !is_null($category['category']['parent_id'])) {
                if(!isset($counts[$category['category']['parent_id']])) {
                    $counts[$category['category']['parent_id']] = 0;
                }
                $counts[$category['category']['parent_id']]++;
            }
            $counts[$category['category_id']]++;
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
