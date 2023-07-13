<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('category_tags', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('tag_id');
        });

        Schema::table('category_tags', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('tag_id')->references('id')->on('tags');
        });

        $categoryTags = [
            [
                'category_id' => 5,
                'tag_id' => 1,
            ],
            [
                'category_id' => 5,
                'tag_id' => 2,
            ],
            [
                'category_id' => 6,
                'tag_id' => 3,
            ],
            [
                'category_id' => 7,
                'tag_id' => 4,
            ],
            [
                'category_id' => 7,
                'tag_id' => 5,
            ],
            [
                'category_id' => 7,
                'tag_id' => 6,
            ],
            [
                'category_id' => 7,
                'tag_id' => 7,
            ],
        ];

        DB::table('category_tags')->insert($categoryTags);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_tags');
    }
};
