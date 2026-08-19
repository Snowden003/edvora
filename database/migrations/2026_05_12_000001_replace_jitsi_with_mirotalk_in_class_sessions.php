<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('class_sessions', 'jitsi_room')) {
                $table->renameColumn('jitsi_room', 'room_name');
            }

            if (Schema::hasColumn('class_sessions', 'meet_type')) {
                $table->dropColumn('meet_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('class_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('class_sessions', 'room_name')) {
                $table->renameColumn('room_name', 'jitsi_room');
            }

            if (!Schema::hasColumn('class_sessions', 'meet_type')) {
                $table->enum('meet_type', ['google_meet', 'jitsi'])->default('google_meet')->after('jitsi_room');
            }
        });
    }
};
