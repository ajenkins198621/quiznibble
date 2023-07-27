<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ViewDashboardController extends Controller
{
    private function _getCategories() {
        return Category::select([
            'id', 'category_name'
        ])
            ->with([
                'subCategories:id,category_name,parent_id',
                'questionCount'
            ])
            ->whereNull('parent_id')
            ->get()
            ->toArray();
    }

    public function viewDashboard(Request $request, int $mainCategoryId = -1, int $subCategoryId = -1) : Response {
        return Inertia::render('Dashboard', [
            'mainCategoryId' => $mainCategoryId,
            'subCategoryId' => $subCategoryId,
            'categories' => $this->_getCategories(),
        ]);
    }
}
