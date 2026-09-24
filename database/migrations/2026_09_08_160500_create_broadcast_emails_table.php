<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('broadcast_emails')) {
            Schema::create('broadcast_emails', function (Blueprint $table) {
                $table->id();
                $table->string('subject');
                $table->string('preheader')->nullable();
                $table->text('body');
                $table->string('banner_image')->nullable();
                $table->string('target_audience')->default('students'); // students, teachers, all
                $table->unsignedInteger('recipients_count')->default(0);
                $table->string('cta_text')->nullable();
                $table->string('cta_url')->nullable();
                $table->foreignId('sent_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_emails');
    }
};
