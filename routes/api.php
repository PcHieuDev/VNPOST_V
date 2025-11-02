<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\DanhMucController;
use App\Http\Controllers\API\BaoCaoController;
use App\Http\Controllers\API\AuthController;
use Illuminate\Support\Facades\Auth;



// --- Danh mục ---
Route::prefix('danh-muc')->group(function () {
    Route::get('don-vi', [DanhMucController::class, 'getDonViHanhChinh']);
    Route::get('buu-cuc', [DanhMucController::class, 'getBuuCuc']);
    Route::get('dich-vu', [DanhMucController::class, 'getDichVu']);
});

// --- Báo cáo giao dịch ---
Route::post('bao-cao', [BaoCaoController::class, 'store']);
Route::get('bao-cao', [BaoCaoController::class, 'index']);
Route::get('/statistics', [BaoCaoController::class, 'getChartData']);


Route::post('/logout', function() {
    Auth::logout();
    return response()->json(['message' => 'Logged out successfully']);
});

Route::post('/register', [AuthController::class, 'register'])->name('register');


// --- Đơn vị Hành chính ---
Route::post('donvihanhchinh', [DanhMucController::class, 'storeDonViHanhChinh']); // Thêm (Create)
Route::put('donvihanhchinh/{id}', [DanhMucController::class, 'updateDonViHanhChinh']); // Sửa (Update)
Route::delete('donvihanhchinh/{id}', [DanhMucController::class, 'destroyDonViHanhChinh']); // Xóa (Delete)

// --- Bưu cục ---
Route::post('buucuc', [DanhMucController::class, 'storeBuuCuc']); 
Route::delete('buucuc/{id}', [DanhMucController::class, 'destroyBuuCuc']);
Route::put('buucuc/{id}', [DanhMucController::class, 'updateBuuCuc']);

// --- Dịch vụ ---
Route::post('dichvu', [DanhMucController::class, 'storeDichVu']);
Route::get('dichvu', [DanhMucController::class, 'getDichVu']);
Route::put('dichvu/{id}', [DanhMucController::class, 'updateDichVu']);
Route::delete('dichvu/{id}', [DanhMucController::class, 'destroyDichVu']);
