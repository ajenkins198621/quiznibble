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
        Schema::table('questions', function (Blueprint $table) {
            $table->boolean('active')->default(true)->after('question');
            $table->boolean('flagged')->default(false)->after('detail_url');
            $table->string('flagged_reason')->nullable()->after('flagged');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('active');
            $table->dropColumn('flagged');
            $table->dropColumn('flagged_reason');
        });
    }
};
