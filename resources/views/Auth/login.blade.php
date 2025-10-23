<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="stylesheet" href="{{ asset('css/report_styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>Đăng nhập - Vietnam Post</title>
</head>
<body> 
    
   @include('layouts.header')

    <div class="banner-wave"></div>

    <div style="min-height: calc(100vh - 150px); display: flex; justify-content: center; align-items: flex-start; padding-top: 50px;">
        <div class="login-card">
            <h2>Đăng Nhập Hệ Thống</h2>
            
            <!-- Hiển thị lỗi nếu có -->
            @if(session('error'))
                <div style="color: #dc3545; margin-bottom: 15px; text-align: center; font-weight: bold;">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Form đăng nhập -->
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">
                    Đăng Nhập
                </button>
            </form>
            
            <p style="margin-top: 20px; text-align: center; font-size: 14px;">
                Chưa có tài khoản? <a href="/register" style="color: #0066cc; font-weight: 600;">Đăng ký ngay</a>
            </p>
        </div>
    </div>
    <script src="{{ asset('js/auth_handler.js') }}"></script>
</body>
</html>
