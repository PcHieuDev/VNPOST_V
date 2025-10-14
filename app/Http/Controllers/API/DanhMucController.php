<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DonViHanhChinh;
use App\Models\BuuCuc;
use App\Models\DichVu;
use Illuminate\Http\Request;

class DanhMucController extends Controller
{
    // Hàm lấy danh sách Đơn vị Hành chính (Huyện)
    public function getDonViHanhChinh()
    {
        $donVis = DonViHanhChinh::all();
        return response()->json($donVis);
    }

    // Hàm lấy danh sách Bưu cục (tùy chọn lọc theo Huyện/Đơn vị)
    public function getBuuCuc(Request $request)
    {
        $query = BuuCuc::query();

        // Lọc theo DonViID nếu có truyền qua query parameter (ví dụ: /api/buucuc?donViId=1)
        if ($request->has('donViId')) {
            $query->where('DonViID', $request->donViId);
        }

        $buuCucs = $query->get();
        return response()->json($buuCucs);
    }

    // Hàm lấy danh sách Dịch vụ
    public function getDichVu()
    {
        $dichVus = DichVu::all();
        return response()->json($dichVus);
    }
}