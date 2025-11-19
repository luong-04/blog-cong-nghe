<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Tác giả
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); // Danh mục
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content'); // Nội dung bài viết
            $table->string('featured_image')->nullable(); // Ảnh đại diện
            $table->string('status')->default('draft'); // Trạng thái: nháp/công khai
            $table->integer('view_count')->default(0); // Đếm lượt xem
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};