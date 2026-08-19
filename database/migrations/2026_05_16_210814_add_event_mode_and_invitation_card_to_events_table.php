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
        Schema::table('events', function (Blueprint $table) {
            $table->enum('event_mode', ['online', 'in-person'])->default('online')->after('type');
            $table->enum('invitation_card_type', ['text', 'image', 'pdf'])->nullable()->after('google_maps_url');
            $table->text('invitation_card_content')->nullable()->after('invitation_card_type');
            $table->string('invitation_card_file')->nullable()->after('invitation_card_content');
            $table->json('workshop_details')->nullable()->after('invitation_card_file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['event_mode', 'invitation_card_type', 'invitation_card_content', 'invitation_card_file']);
        });
    }
};
