<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_sessions', function (Blueprint $table) {
            $table->string('jitsi_room')->nullable()->after('meet_link');
            $table->enum('meet_type', ['google_meet', 'jitsi'])->default('google_meet')->after('jitsi_room');
        });
    }

    public function down(): void
    {
        Schema::table('class_sessions', function (Blueprint $table) {
            $table->dropColumn(['jitsi_room', 'meet_type']);
        });
    }
};
