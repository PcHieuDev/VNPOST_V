
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
        <div x-data="authHandler()" class="login-card">
            <h2>Đăng Ký </h2>
            
            <div x-show="error" style="color: #dc3545; margin-bottom: 15px; text-align: center; font-weight: bold;" x-text="error"></div>

            <form @submit.prevent="register">
                <label for="name">Họ và tên</label>
        <input type="text" id="name" x-model="form.name" required placeholder="Nhập họ và tên">
                <label for="email">Email</label>
                <input type="email" id="email" x-model="form.email" required>

                <label for="password">Mật khẩu</label>
                <input type="password" id="password" x-model="form.password" required>
                <label for="password_confirmation">Xác nhận mật khẩu</label>
                <input type="password" id="password_confirmation" x-model="form.password_confirmation" required>

                <button type="submit" :disabled="loading">
                    <span x-show="!loading">Đăng Ký</span>
                    <span x-show="loading">Đang xử lý...</span>
                </button>
            </form>
            <p style="margin-top: 20px; text-align: center; font-size: 14px;">
                Đã có tài khoản? <a href="/login" class="register-link">Đăng nhập ngay</a>
            </p>
        </div>
    </div>
    <script src="{{ asset('js/auth_handler.js') }}"></script>
</body>
</html>