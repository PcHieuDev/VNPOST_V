<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\DonViHanhChinh; // Import Model

class BuuCucSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy ID của các đơn vị hành chính đã tạo
        $huyen_a_id = DonViHanhChinh::where('MaDonVi', 'HA')->first()->DonViID;
        $huyen_b_id = DonViHanhChinh::where('MaDonVi', 'HB')->first()->DonViID;
        $tp_c_id = DonViHanhChinh::where('MaDonVi', 'TPC')->first()->DonViID;

        DB::table('buu_cuc')->insert([
            // Bưu cục thuộc Huyện A
            ['DonViID' => $huyen_a_id, 'TenBuuCuc' => 'BC Trung tâm A', 'MaBuuCuc' => 'TTA', 'created_at' => now(), 'updated_at' => now()],
            ['DonViID' => $huyen_a_id, 'TenBuuCuc' => 'Đại lý 1 Huyện A', 'MaBuuCuc' => 'DL1A', 'created_at' => now(), 'updated_at' => now()],
            
            // Bưu cục thuộc Huyện B
            ['DonViID' => $huyen_b_id, 'TenBuuCuc' => 'BC Huyện B', 'MaBuuCuc' => 'BCB', 'created_at' => now(), 'updated_at' => now()],
            
            // Bưu cục thuộc Thành phố C
            ['DonViID' => $tp_c_id, 'TenBuuCuc' => 'BC Trung tâm TP C', 'MaBuuCuc' => 'TTC', 'created_at' => now(), 'updated_at' => now()],
            ['DonViID' => $tp_c_id, 'TenBuuCuc' => 'ĐL 2 TP C', 'MaBuuCuc' => 'DL2C', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}