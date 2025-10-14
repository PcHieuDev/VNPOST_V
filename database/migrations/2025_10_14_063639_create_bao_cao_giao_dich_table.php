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
        Schema::create('bao_cao_giao_dich', function (Blueprint $table) {
            $table->id('GiaoDichID'); // Khóa chính tự tăng
            $table->unsignedBigInteger('BuuCucID'); // Khóa ngoại
            $table->unsignedBigInteger('DichVuID'); // Khóa ngoại
            $table->date('NgayBaoCao');
            $table->decimal('TongSoTien', 18, 2)->default(0);
            $table->integer('SoLuong')->default(0);

            // Trường lưu dữ liệu chi tiết, sử dụng 'json' để lưu 5-6 trường nhập liệu riêng
            // Nếu bạn dùng MySQL cũ hơn không hỗ trợ JSON, hãy đổi thành 'text'
            $table->json('DuLieuChiTiet')->nullable(); 

            $table->string('GhiChu', 255)->nullable();
            $table->timestamps();

            // Thiết lập Khóa ngoại
            $table->foreign('BuuCucID')->references('BuuCucID')->on('buu_cuc')->onDelete('cascade');
            $table->foreign('DichVuID')->references('DichVuID')->on('dich_vu')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bao_cao_giao_dich');
    }
};