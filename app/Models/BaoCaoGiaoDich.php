<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BaoCaoGiaoDich extends Model
{
    use HasFactory;

    protected $table = 'bao_cao_giao_dich';
    protected $primaryKey = 'GiaoDichID';
    
    protected $fillable = [
        'BuuCucID',
        'DichVuID',
        'NgayBaoCao',
        'TongSoTien',
        'SoLuong',
        'DuLieuChiTiet', // Đây là trường quan trọng
        'GhiChu',
    ];

    // Khai báo Casts: Tự động chuyển đổi trường 'DuLieuChiTiet' thành mảng/đối tượng PHP khi truy xuất.
    protected $casts = [
        'DuLieuChiTiet' => 'json',
        'NgayBaoCao' => 'date', // Để đảm bảo nó được xử lý như ngày tháng
    ];

    /**
     * Mối quan hệ: Báo cáo thuộc về một Bưu cục.
     */
    public function buuCuc(): BelongsTo
    {
        return $this->belongsTo(BuuCuc::class, 'BuuCucID', 'BuuCucID');
    }

    /**
     * Mối quan hệ: Báo cáo thuộc về một Dịch vụ.
     */
    public function dichVu(): BelongsTo
    {
        return $this->belongsTo(DichVu::class, 'DichVuID', 'DichVuID');
    }
}