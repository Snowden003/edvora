<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('password');
            $table->text('bio')->nullable()->after('avatar');
            $table->integer('xp')->default(0)->after('bio');
            $table->string('phone')->nullable()->after('xp');
            $table->string('department')->nullable()->after('phone');
            $table->string('experience_years')->nullable()->after('department');
            $table->string('cv_path')->nullable()->after('experience_years');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'bio', 'xp', 'phone', 'department', 'experience_years', 'cv_path']);
        });
    }
};
