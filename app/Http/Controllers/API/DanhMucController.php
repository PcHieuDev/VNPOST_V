<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DonViHanhChinh;
use App\Models\BuuCuc;
use App\Models\DichVu;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class DanhMucController extends Controller
{
    // Hàm lấy danh sách Đơn vị Hành chính (Huyện)
    public function getDonViHanhChinh()
    {
        $donVis = DonViHanhChinh::orderBy('TenDonVi', 'asc')->get();
        return response()->json($donVis);
    }

    //Create ham thêm mới đơn vị 
    public function storeDonViHanhChinh(Request $request){
        $validate = $request->validate([
            'TenDonVi' => 'required|string|max:255|unique:don_vi_hanh_chinh,TenDonVi',

        ]);

        try{
            $donVi = DonViHanhChinh::create($validate);
            return response()->json([
                'message' => 'Tao moi Don vi hanh chinh thanh cong',
                'data' => $donVi
            ], 201);

        } catch(\Exception $e){
            return response()->json([
                'message' => 'Loi khi tao moi Don vi hanh chinh',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //ham cap nhat don vi hanh chinh
    public function updateDonViHanhChinh(Request $request, $id)
    {
        $donVi = DonViHanhChinh::find($id);
        if(!$donVi){
            return response()->json(['error'=>'Don vi hanh chinh khong ton tai'], 404);
        }

        // Validate dữ liệu đầu vào
        $validate = $request->validate(
            [
                'TenDonVi' => 'required|string|max:255|unique:din_vi_hanh_chinh,TenDonVi,' . $id,

            ]
            );
            $donVi->update($validate);
            return response()->json([
                'message' => 'Don vi hanh chinh da duoc cap nhat.',
                'data' => $donVi
            ]);
    }

    // xoa don vi hanh chinh
    public function deleteDonViHanhChinh($id)
    {
        $donVi = DonViHanhChinh::find($id);
        if(!$donVi){
            return response()->json(['error'=>'Đơn vị hành chính không tồn tại'], 404);
        } else {
            $donVi->delete();
            return response()->json(['message'=>'Đơn vị hành chính đã được xóa thành công']);
        }

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

    // Hàm tao moi Bưu cục
    public function storeBuuCuc(Request $request)
    {
        $validated = $request->validate(
            [
                'TenBuuCuc' => 'required|string|max:255|unique:buu_cuc,TenBuuCuc',
                'DonViID' => 'required|integer|exists:don_vi_hanh_chinh,DonViID',
            ]
        );
        
        $BuuCuc = BuuCuc::create($validated);
        return response()->json([
            'message' => 'Tao moi Buu cuc thanh cong',
            'data' => $BuuCuc
        ], 201);
    }

    // ham cap nhat buu cuc
    public function updateBuuCuc(Request $request, $id)
    {
        $buuCuc = BuuCuc::find($id);
        if (!$buuCuc) {
            return response()->json(['error' => 'Bưu cục không tồn tại'], 404);
        }

        // Sử dụng cú pháp Rule object để giải quyết lỗi chuỗi validation
        $validated = $request->validate([
            'TenBuuCuc' => [
                'required',
                'string',
                'max:255',
                // Rule::unique() tránh các lỗi string concatenation
                Rule::unique('buu_cuc', 'TenBuuCuc')->ignore($id, 'BuuCucID'),
            ],
            'DonViID' => 'required|integer|exists:don_vi_hanh_chinh,DonViID', 
        ]);

        $buuCuc->update($validated);
        return response()->json([
            'message' => 'Bưu cục đã được cập nhật thành công.',
            'data' => $buuCuc
        ], 200);
    }
    

    // ham xoa buu cuc
    public function deleteBuuCuc($id)
    {
        $BuuCuc = BuuCuc::find($id);
        if($BuuCuc){
            $BuuCuc -> delete();
            return response()->json(['message'=>'Buu cuc da duoc xoa thanh cong']);

        }
        else{
            return response()->json(['error'=>'Buu cuc khong ton tai'], 404);
    }
}



    // Hàm lấy danh sách Dịch vụ
    public function getDichVu()
    {
        $dichVus = DichVu::all();
        return response()->json($dichVus);
    }

    //ham tao moi dich vu
    public function storeDichVu(Request $Request){
        $validated = $Request->validate([
            'TenDichVu'=> 'required|string|max:255|unique:dich_vu,TenDichVu',
        ]);
        $DichVu = DichVu::create($validated);
        return response()->json([
            'message' => 'Tao moi Dich vu thanh cong',
            'data' => $DichVu
        ], 201);
    }

    // ham cap nhat dich vu
    public function updateDichVu(Request $request, $id)
    {
        $dichvu = DichVu::find($id);
        if(!$dichvu){
            return response()->json(['error'=>'Dich vu khong ton tai'], 404);
        }
        $validate = $request->validate(
            [
                'TenDichVu' => 'required|string|max:255',
                
                Rule::unique('dich_vu', 'TenDichVu')->ignore($id, 'DichVuID'),
            ]
            );
            $dichvu->update($validate);
            return response()->json([
                'message' => 'Dich vu da duoc cap nhat.',
                'data' => $dichvu
            ]);
    }

    // ham xoa dich vu
    public function deleteDichVu($id)
    {
        $dichvu = DichVu::find($id);
        if($dichvu){
            $dichvu -> delete();
            return response()->json(['message'=>'Dich vu da duoc xoa thanh cong']);

        }
        else{
            return response()->json(['error'=>'Dich vu khong ton tai'], 404);
    }
}
}