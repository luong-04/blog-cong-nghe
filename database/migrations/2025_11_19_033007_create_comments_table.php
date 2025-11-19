<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            // user_id có thể null nếu khách vãng lai bình luận
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('author_name');
            $table->string('author_email');
            $table->text('content');
            $table->string('status')->default('pending'); // Chờ duyệt
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};