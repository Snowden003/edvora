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
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('role_title');
            $table->string('department')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('telegram')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('github')->nullable();
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->string('employee_id')->nullable();
            $table->date('joined_date')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
