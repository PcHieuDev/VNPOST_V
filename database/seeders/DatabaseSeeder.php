<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Gọi các Seeder theo thứ tự cần thiết để đảm bảo khóa ngoại hoạt động
        $this->call([
            DonViHanhChinhSeeder::class, // Phải chạy trước
            DichVuSeeder::class, // Có thể chạy song song
            BuuCucSeeder::class, // Phải chạy sau DonViHanhChinhSeeder
        ]);
    }
}