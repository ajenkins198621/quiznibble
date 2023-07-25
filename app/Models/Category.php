<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;


    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function subCategories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function childQuestions()
    {
        return $this->hasMany(Question::class);
    }

    public function allQuestions() {
        return $this->hasMany(CategoryTag::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'category_tags');
    }

}
