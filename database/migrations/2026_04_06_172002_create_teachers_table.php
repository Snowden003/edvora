<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('specialization')->nullable();
            $table->text('expertise')->nullable();
            $table->integer('years_of_experience')->default(0);
            $table->string('linkedin')->nullable();
            $table->string('github')->nullable();
            $table->string('website')->nullable();
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('total_students')->default(0);
            $table->integer('total_courses')->default(0);
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
