<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('student_profiles', 'whatsapp_number')) {
                $table->string('whatsapp_number')->nullable()->after('phone_number');
            }
        });

        // Make current_address nullable in case it was non-null
        if (Schema::hasColumn('student_profiles', 'current_address')) {
            DB::statement("ALTER TABLE `student_profiles` MODIFY COLUMN `current_address` VARCHAR(255) NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('student_profiles', 'whatsapp_number')) {
                $table->dropColumn('whatsapp_number');
            }
        });
    }
};
