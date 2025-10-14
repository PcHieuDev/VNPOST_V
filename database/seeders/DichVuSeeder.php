<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\DichVu;
use App\Models\BaoCaoGiaoDich; // nếu có model báo cáo

class DichVuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. TẠO CẤU TRÚC TRƯỜNG CHO DỊCH VỤ NGÂN HÀNG
        // Đây là một mảng PHP, Laravel Model sẽ tự động chuyển thành JSON.
        $nganHangConfig = [
            ["key" => "TKBDSOTIEN", "label" => "TKBĐ SỐ TIỀN (Huy động trong ngày)", "type" => "number"],
            ["key" => "TDKHDONGTIEN", "label" => "TÍN DỤNG HƯU TRÍ SỐ TIỀN (Giải ngân)", "type" => "number"],
            ["key" => "TDNGCOCONGTIEN", "label" => "TÍN DỤNG NGƯỜI CÓ CÔNG (Giải ngân)", "type" => "number"],
            ["key" => "VTSBDAUTOT", "label" => "VAY TÀI SẢN ĐẢM BẢO (MUA Ô TÔ)", "type" => "number"],
            ["key" => "VTSBDKHAC", "label" => "VAY TÀI SẢN BẢO ĐẢM KHÁC", "type" => "number"],
            ["key" => "VK_FIFOFE", "label" => "VAY KHÁC: FIFO, FE (Giải ngân)", "type" => "number"]
        ];

        // 2. XOÁ DỮ LIỆU CŨ TRONG BẢNG (Quan trọng khi chạy lại Seeder)
        // Tắt kiểm tra foreign key để tránh lỗi khi truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Nếu có bảng báo cáo tham chiếu, truncate bảng con trước (khuyến nghị)
        if (class_exists(BaoCaoGiaoDich::class)) {
            BaoCaoGiaoDich::truncate();
        }

        // Truncate bảng dịch vụ
        DichVu::truncate();

        // chuẩn bị dữ liệu trước khi insert, encode mảng sang JSON
        $items = [
            ['TenDichVu' => 'BCCP', 'LoaiDichVu' => 'Chính', 'CauHinhTruong' => null, 'created_at' => now(), 'updated_at' => now()],
            ['TenDichVu' => 'BẢO HIỂM', 'LoaiDichVu' => 'Chính', 'CauHinhTruong' => null, 'created_at' => now(), 'updated_at' => now()],
            [
                'TenDichVu' => 'NGÂN HÀNG',
                'LoaiDichVu' => 'Chính',
                'CauHinhTruong' => $nganHangConfig, // mảng sẽ được chuyển JSON ở dưới
                'created_at' => now(),
                'updated_at' => now()
            ],
            ['TenDichVu' => 'DỊCH VỤ SỐ', 'LoaiDichVu' => 'Khác', 'CauHinhTruong' => null, 'created_at' => now(), 'updated_at' => now()],
            ['TenDichVu' => 'BHXH-YT', 'LoaiDichVu' => 'Khác', 'CauHinhTruong' => null, 'created_at' => now(), 'updated_at' => now()],
            ['TenDichVu' => 'HÀNG TIÊU DÙNG', 'LoaiDichVu' => 'Khác', 'CauHinhTruong' => null, 'created_at' => now(), 'updated_at' => now()],
            ['TenDichVu' => 'TIỀN ĐIỆN, DOANH THU KHÁC', 'LoaiDichVu' => 'Khác', 'CauHinhTruong' => null, 'created_at' => now(), 'updated_at' => now()],
            ['TenDichVu' => 'CỐ ĐỊNH', 'LoaiDichVu' => 'Kế toán', 'CauHinhTruong' => null, 'created_at' => now(), 'updated_at' => now()],
        ];

        // encode các trường mảng sang JSON trước khi insert (insert() không dùng model casting)
        foreach ($items as &$it) {
            if (isset($it['CauHinhTruong']) && is_array($it['CauHinhTruong'])) {
                $it['CauHinhTruong'] = json_encode($it['CauHinhTruong'], JSON_UNESCAPED_UNICODE);
            }
        }
        unset($it);

        DichVu::insert($items);

        // Bật lại kiểm tra foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}