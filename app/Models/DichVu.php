<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DichVu extends Model
{
    use HasFactory;

    protected $table = 'dich_vu';
    protected $primaryKey = 'DichVuID';
    
    protected $fillable = [
        'TenDichVu',
        'LoaiDichVu',
        'CauHinhTruong', // Đã thêm vào fillable
    ];

    // CẤU HÌNH QUAN TRỌNG
    protected $casts = [
        'CauHinhTruong' => 'array', // Chuyển đổi tự động sang mảng/đối tượng PHP
    ];

    /**
     * Mối quan hệ: Một Dịch vụ có nhiều Báo cáo Giao dịch.
     */
    public function baoCaoGiaoDichs(): HasMany
    {
        return $this->hasMany(BaoCaoGiaoDich::class, 'DichVuID', 'DichVuID');
    }
}