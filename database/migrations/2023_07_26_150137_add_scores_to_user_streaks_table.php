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
        Schema::table('user_streaks', function (Blueprint $table) {
            $table->integer('day_score')->default(0)->after('last_quiz_date');
            $table->integer('week_score')->default(0)->after('day_score');
            $table->integer('total_score')->default(0)->after('week_score');
            $table->integer('day_score_record')->default(0)->after('total_score');
            $table->integer('week_score_record')->default(0)->after('day_score_record');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_streaks', function (Blueprint $table) {
            $table->dropColumn('day_score');
            $table->dropColumn('week_score');
            $table->dropColumn('total_score');
        });
    }
};
