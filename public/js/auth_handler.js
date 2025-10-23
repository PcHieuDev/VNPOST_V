function authHandler() {
    return {
        form: { 
            name: '',
            email: '',
            password: '',
            password_confirmation: ''
        },
        loading: false,
        error: '',

        // === HELPER FUNCTIONS ===

        // 1. Hàm đọc Cookie
        getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
        },
        
        // 2. Hàm thiết lập Session và CSRF Cookie (BẮT BUỘC cho Fetch API)
        async ensureSessionCookie() {
            // Chỉ chạy /sanctum/csrf-cookie nếu chưa có XSRF-TOKEN trong cookie
            if (this.getCookie('XSRF-TOKEN')) {
                return; 
            }
            
            // Nếu bạn không dùng Laravel Sanctum, bạn cần cài đặt nó: composer require laravel/sanctum
            const response = await fetch('/sanctum/csrf-cookie', {
                method: 'GET' 
            });

            if (!response.ok) {
                throw new Error('Không thể thiết lập Session Cookie. Vui lòng thử lại.');
            }
        },
        
        // Hàm trích xuất lỗi Validation từ Laravel
        extractErrorMessage(result) {
            if (result.errors) {
                const firstErrorKey = Object.keys(result.errors)[0];
                return result.errors[firstErrorKey][0];
            }
            return result.message || 'Lỗi hệ thống không xác định.';
        },
        
        // === 1. LOGIN ===
        async login() {
            this.loading = true; this.error = '';
            
            try {
                // BƯỚC QUAN TRỌNG: Thiết lập Session Cookie
                await this.ensureSessionCookie(); 

                const loginData = { email: this.form.email, password: this.form.password };
                
                const response = await fetch('/api/auth/login', { 
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        // Sử dụng X-XSRF-TOKEN đọc từ Cookie
                        'X-XSRF-TOKEN': this.getCookie('XSRF-TOKEN') 
                    },
                    body: JSON.stringify(loginData)
                });
                
                if (response.ok) {
                    sessionStorage.setItem('user', JSON.stringify(result.user)); 
                    // Đăng nhập thành công, chuyển hướng thẳng
                    window.location.href = '/'; 
                    
                } else {
                    const result = await response.json();
                    this.error = result.message || 'Lỗi đăng nhập.';
                }
            } catch (e) {
                this.error = e.message || 'Lỗi hệ thống không xác định.';
            } finally {
                this.loading = false;
            }
        },
        
        // === 2. REGISTER ===
        async register() {
            this.loading = true; this.error = '';

            // Kiểm tra mật khẩu
            if (this.form.password !== this.form.password_confirmation) {
                 this.error = 'Mật khẩu xác nhận không khớp.'; this.loading = false; return;
            }
            
            try {
                // BƯỚC QUAN TRỌNG: Thiết lập Session Cookie
                await this.ensureSessionCookie(); 

                const response = await fetch('/api/auth/register', { 
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        // Sử dụng X-XSRF-TOKEN đọc từ Cookie
                        'X-XSRF-TOKEN': this.getCookie('XSRF-TOKEN') 
                    },
                    body: JSON.stringify(this.form)
                });

                if (response.ok) {
                    window.location.href = '/';
                } else {
                    const result = await response.json();
                    // Sử dụng hàm helper để hiển thị lỗi validation rõ ràng hơn
                    this.error = this.extractErrorMessage(result);
                }
            } catch (e) {
                this.error = e.message || 'Lỗi hệ thống không xác định.';
            } finally {
                this.loading = false;
            }
        },

        async logout() {
            this.loading = true; this.error = '';
            try {
                const response = await fetch('/api/auth/logout', {
                    method: 'POST',
                    headers: {
                        'X-XSRF-TOKEN': this.getCookie('XSRF-TOKEN')
                    }
                });
                if (response.ok) {
                    sessionStorage.removeItem('user');
                    window.location.href = '/';
                } else {
                    const result = await response.json();
                    this.error = result.message || 'Lỗi khi đăng xuất.';
                }
            } catch (e) {
                this.error = e.message || 'Lỗi hệ thống không xác định.';
            } finally {
                this.loading = false;
            }
        },

        // check trạng thái lohin
        async checkAuthStatus() {
            await this.ensureSessionCookie(); 
            
            try {
                const response = await fetch('/api/auth/user', {
                    headers: { 'X-XSRF-TOKEN': this.getCookie('XSRF-TOKEN') }
                });
                
                if (response.ok) {
                    this.user = await response.json();
                    this.isLoggedIn = true;
                } else {
                    this.user = null;
                    this.isLoggedIn = false;
                }
            } catch (e) {
                console.error("Lỗi khi kiểm tra trạng thái: ", e);
                this.isLoggedIn = false;
            }
        },
    }
}
