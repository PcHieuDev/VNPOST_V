<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\DanhMucController;
use App\Http\Controllers\API\BaoCaoController;


Route::get('/', function () {
    return view('header');
});
Route::view('/bao-cao', 'report'); 
Route::withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]) // Loại bỏ CSRF
    ->prefix('api') // Vẫn đặt tiền tố /api/
    ->group(function () {
        
        // --- Định tuyến cho Danh mục (Selectors) ---
        Route::prefix('danh-muc')->group(function () {
            // GET /api/danh-muc/don-vi
            Route::get('don-vi', [DanhMucController::class, 'getDonViHanhChinh']); 
            
            // GET /api/danh-muc/buu-cuc
            Route::get('buu-cuc', [DanhMucController::class, 'getBuuCuc']); 
            
            // GET /api/danh-muc/dich-vu
            Route::get('dich-vu', [DanhMucController::class, 'getDichVu']); 
        });

        // --- Định tuyến cho Báo cáo Giao dịch ---
        // POST /api/bao-cao (Không cần token CSRF)
        Route::post('bao-cao', [BaoCaoController::class, 'store']); 

        // GET /api/bao-cao
        Route::get('bao-cao', [BaoCaoController::class, 'index']); 
        
    });
