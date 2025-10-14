<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DonViHanhChinh extends Model
{
    use HasFactory;

    // Khai báo tên bảng (nếu khác với quy tắc đặt tên số nhiều mặc định)
    protected $table = 'don_vi_hanh_chinh'; 
    
    // Khai báo tên khóa chính
    protected $primaryKey = 'DonViID'; 
    
    // Khai báo khóa chính không phải là số nguyên (mặc định là true)
    // Nếu bạn dùng ID tự tăng thì có thể bỏ qua dòng này hoặc để mặc định.
    
    // Khai báo các thuộc tính có thể gán hàng loạt (Mass Assignable)
    protected $fillable = [
        'TenDonVi',
        'MaDonVi',
    ];

    /**
     * Mối quan hệ: Một đơn vị hành chính (Huyện) có nhiều Bưu cục.
     */
    public function buuCucs(): HasMany
    {
        return $this->hasMany(BuuCuc::class, 'DonViID', 'DonViID');
    }
}