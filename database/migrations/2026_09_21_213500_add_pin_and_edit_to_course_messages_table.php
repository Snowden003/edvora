<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('course_messages', 'is_pinned')) {
                $table->boolean('is_pinned')->default(false)->after('body');
            }
            if (!Schema::hasColumn('course_messages', 'is_edited')) {
                $table->boolean('is_edited')->default(false)->after('is_pinned');
            }
        });
    }

    public function down(): void
    {
        Schema::table('course_messages', function (Blueprint $table) {
            $table->dropColumn(['is_pinned', 'is_edited']);
        });
    }
};
