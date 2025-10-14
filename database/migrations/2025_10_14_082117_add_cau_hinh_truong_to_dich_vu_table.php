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
        Schema::table('dich_vu', function (Blueprint $table) {
            // Sử dụng kiểu 'json' cho dữ liệu linh hoạt
            $table->json('CauHinhTruong')->nullable()->after('LoaiDichVu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dich_vu', function (Blueprint $table) {
            // Khi rollback, xóa trường này
            $table->dropColumn('CauHinhTruong');
        });
    }
};