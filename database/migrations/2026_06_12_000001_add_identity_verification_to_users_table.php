<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('tazkira_image')->nullable()->after('cv_path');
            $table->enum('identity_status', ['not_submitted', 'pending', 'approved', 'rejected'])
                  ->default('not_submitted')
                  ->after('tazkira_image');
            $table->text('identity_rejection_reason')->nullable()->after('identity_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['tazkira_image', 'identity_status', 'identity_rejection_reason']);
        });
    }
};
