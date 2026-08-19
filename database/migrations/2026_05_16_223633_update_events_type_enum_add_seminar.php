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
        // Update type enum to include 'seminar'
        \DB::statement("ALTER TABLE events MODIFY type ENUM('workshop', 'webinar', 'conference', 'meetup', 'seminar') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum
        \DB::statement("ALTER TABLE events MODIFY type ENUM('workshop', 'webinar', 'conference', 'meetup') NOT NULL");
    }
};
