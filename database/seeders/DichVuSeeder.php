<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\DichVu;
use App\Models\BaoCaoGiaoDich;

class DichVuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. ĐỊNH NGHĨA CẤU HÌNH TRƯỜNG DỊCH VỤ
        $nganHangConfig = [
            ["key" => "TKBDSOTIEN", "label" => "TKBĐ SỐ TIỀN (Huy động trong ngày)", "type" => "number"],
            ["key" => "TDKHDONGTIEN", "label" => "TÍN DỤNG HƯU TRÍ SỐ TIỀN (Giải ngân)", "type" => "number"],
            ["key" => "TDNGCOCONGTIEN", "label" => "TÍN DỤNG NGƯỜI CÓ CÔNG (Giải ngân)", "type" => "number"],
            ["key" => "VTSBDAUTOT", "label" => "VAY TÀI SẢN ĐẢM BẢO (MUA Ô TÔ)", "type" => "number"],
            ["key" => "VTSBDKHAC", "label" => "VAY TÀI SẢN BẢO ĐẢM KHÁC", "type" => "number"],
            ["key" => "VK_FIFOFE", "label" => "VAY KHÁC: FIFO, FE (Giải ngân)", "type" => "number"]
        ];

        $bccpConfig = [
            ["key" => "DT_NOITINH", "label" => "DOANH THU NỘI TỈNH (Bao gồm thuế)", "type" => "number"],
            ["key" => "DT_LIENTINH", "label" => "DOANH THU LIÊN TỈNH (Bao gồm thuế)", "type" => "number"],
            ["key" => "DT_QUOCTE", "label" => "DOANH THU QUỐC TẾ (Bao gồm thuế)", "type" => "number"],
            ["key" => "DT_TEM", "label" => "TEM (Doanh thu tem)", "type" => "number"],
            ["key" => "DT_HCC_CP", "label" => "DOANH THU HCC CHUYỂN PHÁT", "type" => "number"],
            ["key" => "SL_HCC_CAPDOI", "label" => "Cấp đổi Hành chính công (Sản lượng)", "type" => "number"]
        ];
        // --- Cấu hình BẢO HIỂM
       // Sử dụng 'number' cho tất cả các trường tiền và 'SL' (Sản lượng)
        $baoHiemConfig = [
            ["key" => "SL_BHXM", "label" => "BHXM (Số ấn chỉ phát sinh trong ngày)", "type" => "number"],
            ["key" => "ST_BHXO_DS", "label" => "BHXO DÂN SỰ (Số tiền)", "type" => "number"],
            ["key" => "ST_BHXO_VC", "label" => "BHXO VẬT CHẤT (Số tiền)", "type" => "number"],
            ["key" => "SL_BHASBD", "label" => "BH ASBĐ (Số ấn chỉ phát sinh trong ngày)", "type" => "number"],
            ["key" => "SL_BHTD_HGĐ", "label" => "BH Toàn diện hộ gia đình (PVI) (Số ấn chỉ)", "type" => "number"],
            ["key" => "ST_DAIICHI_BM", "label" => "DAIICHI BÁN MỚI (Số tiền thu được)", "type" => "number"],
            ["key" => "ST_DAIICHI_TT", "label" => "DAIICHI THU TÁI TỤC (Số tiền thu được)", "type" => "number"],
            ["key" => "ST_BH_CHAYNO", "label" => "BH CHÁY NỔ (Số tiền thu được)", "type" => "number"],
            ["key" => "ST_BH_HUUTRI", "label" => "BH HƯU TRÍ (Số tiền thu được)", "type" => "number"],
            ["key" => "ST_BH_CONNGUOI", "label" => "BH CON NGƯỜI (Số tiền thu được)", "type" => "number"],
            ["key" => "ST_BH_DULICH", "label" => "BH DU LỊCH (Số tiền thu được)", "type" => "number"],
            ["key" => "ST_BH_TSKT", "label" => "BH TÀI SẢN KỸ THUẬT (Số tiền thu được)", "type" => "number"]
        ];


        // 2. XOÁ DỮ LIỆU CŨ AN TOÀN
        Schema::disableForeignKeyConstraints();

        // Nếu có bảng báo cáo tham chiếu, truncate bảng con trước
        if (class_exists(BaoCaoGiaoDich::class)) {
            BaoCaoGiaoDich::truncate();
        }

        // Truncate bảng dịch vụ
        DichVu::truncate();

        $items = [
            [
                'TenDichVu' => 'BCCP',
                'LoaiDichVu' => 'Chính',
                'CauHinhTruong' => $bccpConfig,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'TenDichVu' => 'BẢO HIỂM',
                'LoaiDichVu' => 'Chính',
                'CauHinhTruong' => $baoHiemConfig, // <-- GÁN CẤU HÌNH BẢO HIỂM
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'TenDichVu' => 'NGÂN HÀNG',
                'LoaiDichVu' => 'Chính',
                'CauHinhTruong' => $nganHangConfig,
                'created_at' => now(),
                'updated_at' => now()
            ],
            ['TenDichVu' => 'DỊCH VỤ SỐ', 'LoaiDichVu' => 'Khác', 'CauHinhTruong' => null, 'created_at' => now(), 'updated_at' => now()],
            ['TenDichVu' => 'BHXH-YT', 'LoaiDichVu' => 'Khác', 'CauHinhTruong' => null, 'created_at' => now(), 'updated_at' => now()],
            ['TenDichVu' => 'HÀNG TIÊU DÙNG', 'LoaiDichVu' => 'Khác', 'CauHinhTruong' => null, 'created_at' => now(), 'updated_at' => now()],
            ['TenDichVu' => 'TIỀN ĐIỆN, DOANH THU KHÁC', 'LoaiDichVu' => 'Khác', 'CauHinhTruong' => null, 'created_at' => now(), 'updated_at' => now()],
            ['TenDichVu' => 'CỐ ĐỊNH', 'LoaiDichVu' => 'Kế toán', 'CauHinhTruong' => null, 'created_at' => now(), 'updated_at' => now()],
        ];

        // Mã hóa mảng thành chuỗi JSON trước khi insert vào DB
        foreach ($items as &$it) {
            if (is_array($it['CauHinhTruong'])) {
                // Sử dụng JSON_UNESCAPED_UNICODE để giữ nguyên tiếng Việt
                $it['CauHinhTruong'] = json_encode($it['CauHinhTruong'], JSON_UNESCAPED_UNICODE);
            }
        }
        unset($it);

        // 4. CHÈN DỮ LIỆU
        DichVu::insert($items);

        // Bật lại kiểm tra foreign key
        Schema::enableForeignKeyConstraints();
    }
}
