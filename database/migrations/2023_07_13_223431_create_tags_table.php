<?php

use Carbon\Carbon;
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
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('tag_name');
            $table->timestamps();
        });

        $tags = [
            'Merge Sort',
            'Insertion Sort',
            'Binary Search',
            'Bloom Filter',
            'Hash Tables',
            'Count-Min Sketch',
            'Robin-Karp',
        ];

        $tagInserts = [];
        foreach ($tags as $tag) {
            $tagInserts[] = [
                'tag_name' => $tag,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('tags')->insert($tagInserts);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
