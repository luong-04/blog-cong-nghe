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
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable(); // Tên quảng cáo
            $table->string('image');             // Đường dẫn ảnh
            $table->string('link')->nullable();  // Link khi bấm vào
            $table->boolean('is_active')->default(true); // Trạng thái bật/tắt
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
