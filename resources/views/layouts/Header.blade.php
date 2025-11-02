<div class="header">
    <div class="header-content">
        <div class="logo">
            <img class="logo-icon" src="https://vietnampost.vn/apps/frontend/images/header/logo-fuild.png">
            <div class="logo-text">VIETNAM POST</div>
        </div>
        <nav class="main-nav">
            @php
                use Illuminate\Support\Facades\Request;
            @endphp
            @if (Request::is('login'))
                <a href="{{ url('/') }}" style="color: #ffa500;">ĐĂNG NHẬP</a>
            @elseif (Request::is('/'))
                <a href="{{ url('/') }}" style="color: #ffa500;">BÁO CÁO</a>
            @endif 
            <a href="#">DOANH NGHIỆP</a>
            <a href="#">HÀNH CHÍNH CÔNG</a>
        </nav>
        @if (session()->has('level'))
            <div class="header-right">
                <!-- Thông tin user -->
                <div class="user-info">
                    <span class="user-icon">👤</span>
                    <span class="user-name">{{ session('name') }}</span>
                </div>

                <!-- Đăng xuất thông qua route -->
                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button class="header-btn login" type="submit">
                        🔓 Đăng xuất
                    </button>
                </form>
                <div class="flag-btn"></div>
            </div>
        @endif
    </div>
</div>
<div class="banner-wave"></div>

<style>
    .user-info {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 8px 16px;
        border-radius: 25px;
        margin-right: 12px;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        animation: fadeIn 0.4s ease-out;
    }

    .user-icon {
        font-size: 18px;
        filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.2));
    }

    .user-name {
        color: #ffffff;
        font-weight: 600;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateX(-10px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
</style>
