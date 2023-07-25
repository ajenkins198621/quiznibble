<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function grandparentCategory()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function questionType()
    {
        return $this->belongsTo(QuestionType::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function questionTags() {
        return $this->hasMany(QuestionTag::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'question_tags');
    }

}
