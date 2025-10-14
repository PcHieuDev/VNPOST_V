<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DonViHanhChinhSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('don_vi_hanh_chinh')->insert([
            [
                'TenDonVi' => 'Huyện A', 
                'MaDonVi' => 'HA', 
                'created_at' => now(), 
                'updated_at' => now()
            ],
            [
                'TenDonVi' => 'Huyện B', 
                'MaDonVi' => 'HB', 
                'created_at' => now(), 
                'updated_at' => now()
            ],
            [
                'TenDonVi' => 'Thành phố C', 
                'MaDonVi' => 'TPC', 
                'created_at' => now(), 
                'updated_at' => now()
            ],
        ]);
    }
}