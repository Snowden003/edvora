<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roadmap_stages', function (Blueprint $table) {
            $table->id();
            $table->string('stage_number');
            $table->string('title');
            $table->text('description');
            $table->string('icon')->default('fas fa-circle');
            $table->string('image_url')->nullable();
            $table->string('duration')->nullable();
            $table->json('skills')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roadmap_stages');
    }
};
