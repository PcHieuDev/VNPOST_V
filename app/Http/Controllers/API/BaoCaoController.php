<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BaoCaoGiaoDich;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BaoCaoController extends Controller
{
    // 1. Hàm lưu báo cáo giao dịch (Khi nhân viên nhấn nút 'Báo cáo')
    public function store(Request $request)
    {
        // 1. Định nghĩa quy tắc kiểm tra dữ liệu
        $validator = Validator::make($request->all(), [
            'BuuCucID' => 'required|exists:buu_cuc,BuuCucID',
            'DichVuID' => 'required|exists:dich_vu,DichVuID',
            'NgayBaoCao' => 'required|date',
            'TongSoTien' => 'nullable|numeric|min:0',
            'SoLuong' => 'nullable|integer|min:0',
            'DuLieuChiTiet' => 'required|array', // Đảm bảo đây là mảng JSON
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // 2. Lưu dữ liệu
        $baoCao = BaoCaoGiaoDich::create([
            'BuuCucID' => $request->BuuCucID,
            'DichVuID' => $request->DichVuID,
            'NgayBaoCao' => $request->NgayBaoCao,
            'TongSoTien' => $request->TongSoTien ?? 0,
            'SoLuong' => $request->SoLuong ?? 0,
            // Eloquent Model sẽ tự động chuyển mảng PHP thành chuỗi JSON khi lưu
            'DuLieuChiTiet' => $request->DuLieuChiTiet, 
            'GhiChu' => $request->GhiChu,
        ]);

        return response()->json([
            'message' => 'Báo cáo đã được lưu thành công!',
            'data' => $baoCao,
        ], 201);
    }

    // 2. Hàm xem danh sách báo cáo (có thể thêm lọc/phân trang nếu cần)
    public function index()
    {
        // Tải các mối quan hệ (Bưu cục và Dịch vụ) để hiển thị tên thay vì ID
        $baoCaos = BaoCaoGiaoDich::with(['buuCuc', 'dichVu'])
            ->latest() // Sắp xếp theo ngày mới nhất
            ->paginate(15); // Phân trang 15 mục

        return response()->json($baoCaos);
    }
}