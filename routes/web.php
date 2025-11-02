<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use Illuminate\Support\Facades\Auth;




Route::get('/', function () {
    if (!Auth::check()) {
        return redirect('/login')->with('error', 'Vui lòng đăng nhập trước!');
    }
    return view('report');
});
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::get('/register', function () {
    return view('auth.register'); 
})->name('register');
Route::get('/show_report', function () {
    if (!Auth::check()) {
        return redirect('/login')->with('error', 'Vui lòng đăng nhập trước!');
    }
    return view('show_report');
})->name('show_report');