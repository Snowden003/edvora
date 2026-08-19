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
        Schema::table('courses', function (Blueprint $table) {
            // Course Info fields
            $table->boolean('has_certificate')->default(true);
            $table->boolean('has_lifetime_access')->default(true);
            $table->boolean('has_money_back')->default(true);
            $table->boolean('has_downloadable_resources')->default(true);
            $table->boolean('has_community_access')->default(true);
            $table->boolean('has_mobile_access')->default(true);

            // Course Info badges
            $table->boolean('show_certificate_badge')->default(true);
            $table->boolean('show_duration_badge')->default(true);
            $table->boolean('show_students_badge')->default(true);
            $table->boolean('show_level_badge')->default(true);
            $table->boolean('show_category_badge')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'has_certificate',
                'has_lifetime_access',
                'has_money_back',
                'has_downloadable_resources',
                'has_community_access',
                'has_mobile_access',
                'show_certificate_badge',
                'show_duration_badge',
                'show_students_badge',
                'show_level_badge',
                'show_category_badge',
            ]);
        });
    }
};
