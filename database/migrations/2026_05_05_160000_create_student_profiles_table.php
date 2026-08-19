<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->unique();

            // ─── Personal Information (Required) ───
            $table->string('first_name');
            $table->string('last_name');
            $table->string('father_name');
            $table->enum('gender', ['male', 'female']);
            $table->date('date_of_birth');
            $table->string('national_id'); // تذکره نمبر
            $table->string('phone_number');

            // ─── Personal Information (Optional) ───
            $table->string('passport_number')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('nickname')->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
            $table->enum('blood_type', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable();

            // ─── Address Information (Required) ───
            $table->string('province');
            $table->string('district');
            $table->string('current_address');

            // ─── Address Information (Optional) ───
            $table->string('permanent_address')->nullable();
            $table->string('postal_code')->nullable();

            // ─── Education Background (Required) ───
            $table->string('last_education_level'); // e.g. 12th grade, bachelor, etc.
            $table->string('last_school_name');

            // ─── Education Background (Optional) ───
            $table->year('graduation_year')->nullable();
            $table->string('field_of_study')->nullable();
            $table->decimal('gpa', 4, 2)->nullable();
            $table->string('university_name')->nullable();
            $table->text('other_certifications')->nullable();

            // ─── Emergency Contact (Required) ───
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_phone');
            $table->string('emergency_contact_relation');

            // ─── Additional Info (Optional) ───
            $table->text('skills')->nullable();
            $table->text('languages')->nullable();
            $table->text('about_me')->nullable();
            $table->string('profile_photo')->nullable();

            $table->boolean('is_complete')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
