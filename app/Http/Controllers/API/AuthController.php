<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
     public function showLoginForm()
    {
        return view('auth.login'); // Trả về view login
    }
public function register(Request $request){
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => "required|string|email|max:255|unique:users,email",
        'password' => 'required|string|min:8',
    ]);

    $user = User::create([
        'name' => $request->input('name'), // Dùng $request->input() thay vì request('name')
        'email' => $request->input('email'), // Dùng $request->input() thay vì request('email')
        'password' => Hash::make($request->password),
    ]);

    Auth::login($user); // daawng nhaajp sau khi dang ky
    return response()->json(['message' => 'Dang ky thanh cong'], 201);
}

    // ==== dang nhap ----
 public function login(Request $request)
{
    // Validate dữ liệu đầu vào
    $dangnhap = $request->validate([
        'email' => 'required|string|email',
        'password' => 'required|string',
    ]);

    // Kiểm tra thông tin đăng nhập với Auth::attempt()
    if (Auth::attempt(['email' => $dangnhap['email'], 'password' => $dangnhap['password']])) {
        
        $user = Auth::user();
        
        session()->put('id', $user->id);
        session()->put('name', $user->name);
        session()->put('avatar', $user->avatar);
        session()->put('level', $user->level);
                return redirect('/');

    }

    // Trả về phản hồi nếu thông tin đăng nhập không hợp lệ
    return response()->json(['message' => 'Thông tin đăng nhập không hợp lệ'], 401);
}



    //=== dang xuat ====

   public function logout(Request $request)
{
    Auth::logout();  // Đăng xuất người dùng khỏi session
    
    // Xóa tất cả dữ liệu trong session
    $request->session()->flush();
    
     return redirect('/login');

}



}
