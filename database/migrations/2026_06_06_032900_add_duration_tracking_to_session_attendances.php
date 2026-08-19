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
        Schema::table('session_attendances', function (Blueprint $table) {
            $table->timestamp('joined_at')->nullable()->after('status');
            $table->timestamp('left_at')->nullable()->after('joined_at');
            $table->integer('duration_minutes')->default(0)->after('left_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('session_attendances', function (Blueprint $table) {
            $table->dropColumn(['joined_at', 'left_at', 'duration_minutes']);
        });
    }
};
