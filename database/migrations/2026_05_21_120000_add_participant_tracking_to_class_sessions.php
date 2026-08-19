<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_sessions', function (Blueprint $table) {
            $table->timestamp('last_participant_at')->nullable()->after('ended_at')
                  ->comment('Last time a participant joined/left the session');
            $table->integer('participants_count')->nullable()->default(0)->after('last_participant_at')
                  ->comment('Current number of participants in the session');
        });
    }

    public function down(): void
    {
        Schema::table('class_sessions', function (Blueprint $table) {
            $table->dropColumn(['last_participant_at', 'participants_count']);
        });
    }
};
