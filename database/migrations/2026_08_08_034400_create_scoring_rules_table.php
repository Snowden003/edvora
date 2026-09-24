<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('scoring_rules')) {
            Schema::create('scoring_rules', function (Blueprint $table) {
                $table->id();
                $table->string('action_name')->unique();
                $table->integer('default_score')->default(0);
                $table->string('type')->default('manual'); // attendance, assignment, participation, manual
                $table->string('label');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('scoring_rules');
    }
};
