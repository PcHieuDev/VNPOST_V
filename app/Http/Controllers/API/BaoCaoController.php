<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BaoCaoGiaoDich;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class BaoCaoController extends Controller
{
    // 1. Hàm lưu báo cáo giao dịch (Khi nhân viên nhấn nút 'Báo cáo')
    // public function store(Request $request)
    // {
    //     // 1. Định nghĩa quy tắc kiểm tra dữ liệu
    //     $validator = Validator::make($request->all(), [
    //         'BuuCucID' => 'required|exists:buu_cuc,BuuCucID',
    //         'DichVuID' => 'required|exists:dich_vu,DichVuID',
    //         'NgayBaoCao' => 'required|date',
    //         'TongSoTien' => 'nullable|numeric|min:0',
    //         'SoLuong' => 'nullable|integer|min:0',
    //         'DuLieuChiTiet' => 'required|array', // Đảm bảo đây là mảng JSON
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['error' => $validator->errors()], 400);
    //     }

    //     // 2. Lưu dữ liệu
    //     $baoCao = BaoCaoGiaoDich::create([
    //         'BuuCucID'      => $request->BuuCucID,
    //         'DichVuID'      => $request->DichVuID,
    //         'NgayBaoCao'    => $request->NgayBaoCao,
    //         'TongSoTien'    => $request->TongSoTien ?? 0,
    //         'SoLuong'       => $request->SoLuong ?? 0,
    //         'DuLieuChiTiet' => $request->DuLieuChiTiet,
    //         'GhiChu'        => $request->GhiChu,
    //     ]);

    //     return response()->json([
    //         'message' => 'Báo cáo đã được lưu thành công!',
    //         'data' => $baoCao,
    //     ], 201);
    // }
    public function store(Request $request)
    {
        // 1. Validate (SoLuong không còn cần thiết từ client)
        $validator = Validator::make($request->all(), [
            'BuuCucID'       => 'required|exists:buu_cuc,BuuCucID',
            'DichVuID'       => 'required|exists:dich_vu,DichVuID',
            'NgayBaoCao'     => 'required|date',
            'TongSoTien'     => 'required|numeric|min:0',
            'DuLieuChiTiet'  => 'required|array',
            'GhiChu'         => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // 2. Lưu DB với SoLuong = 1
        $baoCao = BaoCaoGiaoDich::create([
            'BuuCucID'      => $request->BuuCucID,
            'DichVuID'      => $request->DichVuID,
            'NgayBaoCao'    => $request->NgayBaoCao,
            'TongSoTien'    => $request->TongSoTien ?? 0,

            // ép cứng SoLuong = 1
            'SoLuong'       => 1,

            'DuLieuChiTiet' => $request->DuLieuChiTiet,
            'GhiChu'        => $request->GhiChu,
        ]);

        return response()->json([
            'message' => 'Báo cáo đã được lưu thành công!',
            'data'    => $baoCao,
        ], 201);
    }



    // Hàm trả dữ liệu biểu đồ
    public function getChartData(Request $request)
    {
        // 1. Range ngày lọc
        $startDate = $request->query('startDate') ?: now()->startOfMonth()->toDateString();
        $endDate   = $request->query('endDate')   ?: now()->toDateString();

        // -----------------------------
        // A. Doanh thu theo Dịch vụ
        //    -> SUM(TongSoTien) GROUP BY TenDichVu
        //    Chart: Pie
        // -----------------------------
        $revenueByServiceRaw = DB::table('bao_cao_giao_dich AS bc')
            ->join('dich_vu AS dv', 'dv.DichVuID', '=', 'bc.DichVuID')
            ->select([
                'dv.TenDichVu AS ten_dich_vu',
                DB::raw('SUM(bc.TongSoTien) AS tong_tien')
            ])
            ->whereBetween('bc.NgayBaoCao', [$startDate, $endDate])
            ->groupBy('dv.TenDichVu')
            ->orderBy('tong_tien', 'DESC')
            ->get();


        $revenueByService = [
            'labels' => $revenueByServiceRaw->pluck('ten_dich_vu'),
            'data'   => $revenueByServiceRaw->pluck('tong_tien')->map(fn($v) => (float)$v),
        ];

        // -----------------------------
        // B. Số lượng báo cáo theo Ngày
        //    -> COUNT(*) GROUP BY NgayBaoCao
        //    Chart: Line
        // -----------------------------
        $reportsByDayRaw = DB::table('bao_cao_giao_dich AS bc')
            ->select([
                'bc.NgayBaoCao AS ngay',
                DB::raw('COUNT(*) AS so_bao_cao')
            ])
            ->whereBetween('bc.NgayBaoCao', [$startDate, $endDate])
            ->groupBy('bc.NgayBaoCao')
            ->orderBy('bc.NgayBaoCao')
            ->get();

        $reportsByDay = [
            'labels' => $reportsByDayRaw->pluck('ngay')->map(function ($d) {
                return Carbon::parse($d)->format('d/m');
            }),
            'data'   => $reportsByDayRaw->pluck('so_bao_cao')->map(fn($v) => (int)$v),
        ];

        // -----------------------------
        // C. Doanh thu theo Đơn vị hành chính
        //    -> JOIN BaoCaoGiaoDich -> buu_cuc -> don_vi_hanh_chinh
        //    -> SUM(TongSoTien) GROUP BY TenDonVi
        //    Chart: Bar
        // -----------------------------
        $revenueByUnitRaw = DB::table('bao_cao_giao_dich AS bc')
            ->join('buu_cuc AS bcuc', 'bcuc.BuuCucID', '=', 'bc.BuuCucID')
            ->join('don_vi_hanh_chinh AS dvhc', 'dvhc.DonViID', '=', 'bcuc.DonViID')
            ->select([
                'dvhc.TenDonVi AS ten_don_vi',
                DB::raw('SUM(bc.TongSoTien) AS tong_doanh_thu')
            ])
            ->whereBetween('bc.NgayBaoCao', [$startDate, $endDate])
            ->groupBy('dvhc.TenDonVi')
            ->orderBy('tong_doanh_thu', 'DESC')
            ->get();

        $revenueByUnit = [
            'labels' => $revenueByUnitRaw->pluck('ten_don_vi'),
            'data'   => $revenueByUnitRaw->pluck('tong_doanh_thu')->map(fn($v) => (float)$v),
        ];

        // -----------------------------
        // Gói trả về cho FE
        // -----------------------------
        return response()->json([
            'range' => [
                'startDate' => $startDate,
                'endDate'   => $endDate,
            ],
            'revenueByService' => $revenueByService, // Pie
            'reportsByDay'     => $reportsByDay,     // Line
            'revenueByUnit'    => $revenueByUnit,    // Bar
        ]);
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
