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
        Schema::create('buu_cuc', function (Blueprint $table) {
            $table->id('BuuCucID'); // Khóa chính tự tăng
            $table->unsignedBigInteger('DonViID'); // Khóa ngoại
            $table->string('TenBuuCuc', 150);
            $table->string('MaBuuCuc', 10)->nullable();
            $table->timestamps();

            // Thiết lập Khóa ngoại
            $table->foreign('DonViID')
                  ->references('DonViID')
                  ->on('don_vi_hanh_chinh')
                  ->onDelete('cascade'); // Tùy chọn: Xóa các bưu cục nếu huyện bị xóa
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buu_cuc');
    }
};