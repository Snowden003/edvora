<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->integer('min_students')->nullable()->after('enrolled_count')
                ->comment('Minimum students required for course to auto-start');
            $table->integer('max_students')->nullable()->after('min_students')
                ->comment('Maximum students allowed to enroll');
            $table->boolean('auto_start_enabled')->default(false)->after('max_students')
                ->comment('Whether course auto-starts when min_students is reached');
            $table->timestamp('started_at')->nullable()->after('end_date')
                ->comment('When the course actually started');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['min_students', 'max_students', 'auto_start_enabled', 'started_at']);
        });
    }
};
