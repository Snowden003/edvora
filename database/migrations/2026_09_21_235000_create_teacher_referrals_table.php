<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->unsignedInteger('clicks_count')->default(0);
            $table->timestamps();

            $table->unique(['teacher_id', 'course_id']);
        });

        Schema::create('teacher_referral_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_referral_id')->constrained('teacher_referrals')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // student
            $table->string('status')->default('registered'); // registered, requested, enrolled
            $table->timestamp('registered_at')->nullable();
            $table->timestamp('enrolled_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->unique(['course_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_referral_records');
        Schema::dropIfExists('teacher_referrals');
    }
};
