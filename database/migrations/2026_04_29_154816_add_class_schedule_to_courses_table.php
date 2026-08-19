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
        Schema::table('courses', function (Blueprint $table) {
            $table->time('primary_class_start')->nullable()->after('duration_hours');
            $table->time('primary_class_end')->nullable()->after('primary_class_start');
            $table->string('primary_class_days', 100)->nullable()->after('primary_class_end');
            $table->text('primary_class_note')->nullable()->after('primary_class_days');

            $table->time('secondary_class_start')->nullable()->after('primary_class_note');
            $table->time('secondary_class_end')->nullable()->after('secondary_class_start');
            $table->string('secondary_class_days', 100)->nullable()->after('secondary_class_end');
            $table->text('secondary_class_note')->nullable()->after('secondary_class_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'primary_class_start', 'primary_class_end', 'primary_class_days', 'primary_class_note',
                'secondary_class_start', 'secondary_class_end', 'secondary_class_days', 'secondary_class_note',
            ]);
        });
    }
};
