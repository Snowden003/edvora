<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Modify the type enum to include fill_in_blank
        DB::statement("ALTER TABLE quiz_questions MODIFY COLUMN type ENUM('multiple_choice', 'true_false', 'short_answer', 'fill_in_blank') DEFAULT 'multiple_choice'");
        
        // Add blank_positions column to store the positions of blanks in the question text
        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->json('blank_positions')->nullable()->after('options');
            $table->json('blank_answers')->nullable()->after('correct_answer'); // For multiple blanks
        });
    }

    public function down(): void
    {
        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->dropColumn(['blank_positions', 'blank_answers']);
        });
        
        DB::statement("ALTER TABLE quiz_questions MODIFY COLUMN type ENUM('multiple_choice', 'true_false', 'short_answer') DEFAULT 'multiple_choice'");
    }
};
