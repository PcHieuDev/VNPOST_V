<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        
        return view('auth.login'); // Trả về view login
    }

    public function showRegisterForm()
    {
        return view('auth.register'); // Trả về view đăng ký
    }
    public function register(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|string|email|max:255|unique:users,email",
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),

        ]);

        Auth::login($user); // Đăng nhập sau khi đăng ký
        return redirect('/')->with('success', 'Đăng ký thành công và đã đăng nhập!');
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
        } else {
            return redirect('/login')->with('error', 'Thông tin đăng nhập không hợp lệ');
        }
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
