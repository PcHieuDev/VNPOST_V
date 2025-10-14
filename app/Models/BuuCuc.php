<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BuuCuc extends Model
{
    use HasFactory;

    protected $table = 'buu_cuc';
    protected $primaryKey = 'BuuCucID';
    
    protected $fillable = [
        'DonViID',
        'TenBuuCuc',
        'MaBuuCuc',
    ];

    /**
     * Mối quan hệ: Một Bưu cục thuộc về (Belongs to) một Đơn vị Hành chính.
     */
    public function donViHanhChinh(): BelongsTo
    {
        return $this->belongsTo(DonViHanhChinh::class, 'DonViID', 'DonViID');
    }

    /**
     * Mối quan hệ: Một Bưu cục có nhiều Báo cáo Giao dịch.
     */
    public function baoCaoGiaoDichs(): HasMany
    {
        return $this->hasMany(BaoCaoGiaoDich::class, 'BuuCucID', 'BuuCucID');
    }
}