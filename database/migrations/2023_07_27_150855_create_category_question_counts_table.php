<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('category_question_counts', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->unique();
            $table->integer('question_count');
        });

        Schema::table('category_question_counts', function (Blueprint $table) {
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_question_counts');
    }
};
