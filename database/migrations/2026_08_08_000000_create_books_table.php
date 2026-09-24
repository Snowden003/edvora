<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('books')) {
            Schema::create('books', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('author')->nullable();
                $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
                $table->string('cover_image')->nullable();
                $table->string('pdf_file');
                $table->boolean('is_published')->default(true);
                $table->unsignedInteger('download_count')->default(0);
                $table->unsignedInteger('view_count')->default(0);
                $table->timestamps();

                $table->index('is_published');
                $table->index('category_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
