<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('points')) {
            Schema::create('points', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade'); // student
                $table->foreignId('course_id')->nullable()->constrained()->onDelete('set null');
                $table->integer('amount'); // positive or negative
                $table->text('reason')->nullable();
                $table->string('type')->default('manual'); // attendance, assignment, participation, manual
                $table->string('related_type')->nullable(); // morph type
                $table->unsignedBigInteger('related_id')->nullable(); // morph id
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null'); // instructor/admin
                $table->timestamps();

                $table->index(['user_id', 'type']);
                $table->index(['course_id', 'created_at']);
                $table->index(['created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('points');
    }
};
