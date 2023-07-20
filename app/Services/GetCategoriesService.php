<?php

namespace App\Services;

class GetCategoriesService {

    public function get() {
        return [
            'categories' => \App\Models\Category::whereNull('parent_id')->get(),
            'sub_categories' => \App\Models\Category::whereNotNull('parent_id')
                ->with('tags')
                ->get(),
        ];
    }

}
