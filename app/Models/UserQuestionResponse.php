<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserQuestionResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'correct_count',
        'incorrect_count',
        'attempt_count'
    ];

}
