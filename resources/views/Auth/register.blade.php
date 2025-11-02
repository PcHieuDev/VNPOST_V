<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- KHÔNG CẦN meta name="csrf-token" nữa vì dùng @csrf -->
    
    <link rel="stylesheet" href="{{ asset('css/report_styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <title>Đăng ký - Vietnam Post</title>
</head>
<body> 
    
    @include('layouts.header')

    <div class="banner-wave"></div>

    <div style="min-height: calc(100vh - 150px); display: flex; justify-content: center; align-items: flex-start; padding-top: 50px;">
        <!-- Đã xóa x-data="authHandler()" -->
        <div class="login-card"> 
            <h2>Đăng Ký </h2>
            
            <!-- HIỂN THỊ LỖI Validation do Laravel trả về -->
            @if ($errors->any())
                <div style="color: #dc3545; margin-bottom: 15px; text-align: center; font-weight: bold;">
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif
            
            <!-- THAY ĐỔI LỚN: Dùng form submit POST và @csrf -->
            <form method="POST" action="{{ route('register') }}">
                @csrf 
                
                <label for="name">Họ và tên</label>
                <!-- Dùng name="" cho submit form truyền thống, và thêm value="" để giữ dữ liệu cũ -->
                <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Nhập họ và tên">
                
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>

                <label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" required>
                
                <label for="password_confirmation">Xác nhận mật khẩu</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>

                <button type="submit">
                    Đăng Ký
                </button>
            </form>
            
            <p style="margin-top: 20px; text-align: center; font-size: 14px;">
                Đã có tài khoản? <a href="/login" class="register-link">Đăng nhập ngay</a>
            </p>
        </div>
    </div>
</body>
</html>
