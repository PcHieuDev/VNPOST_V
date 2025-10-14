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
        Schema::create('don_vi_hanh_chinh', function (Blueprint $table) {
            $table->id('DonViID'); // Khóa chính tự tăng, đặt tên là 'DonViID'
            $table->string('TenDonVi', 100);
            $table->string('MaDonVi', 10)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('don_vi_hanh_chinh');
    }
};