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
        Schema::create('question_types', function (Blueprint $table) {
            $table->id();
            $table->string('question_type');
            $table->timestamps();
        });

        $questionTypes = [
            'Multiple Choice',
            'Multiple Response',
            'True or False',
            'Short Answer',
            'Ordering',
        ];

        $questionTypeInserts = [];
        foreach($questionTypes as $questionType) {
            $questionTypeInserts[] = [
                'question_type' => $questionType,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
            ];
        }

        DB::table('question_types')->insert($questionTypeInserts);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_types');
    }
};
