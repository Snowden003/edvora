<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_requests', function (Blueprint $table) {
            // Add course_id column with foreign key
            $table->foreignId('course_id')->nullable()->after('category_id')
                  ->constrained()->onDelete('cascade');
        });

        // Delete requests for courses that don't exist
        DB::table('course_requests')
            ->whereNotIn('title', function($query) {
                $query->select('title')->from('courses');
            })
            ->delete();
    }

    public function down(): void
    {
        Schema::table('course_requests', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropColumn('course_id');
        });
    }
};
