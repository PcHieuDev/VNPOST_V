<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo cáo - Vietnam Post</title>
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style >@import url('/css/header.css');</style>
</head>
<body x-data="reportPage()">
    <div class="header">
        <div class="header-content">
            <div class="logo">
                <img class="logo-icon" src="https://vietnampost.vn/apps/frontend/images/header/logo-fuild.png">
                <div class="logo-text">VIETNAM POST</div>
            </div>
            <nav class="main-nav">
                <a href="#" style="color: #ffa500;">BÁO CÁO</a>
                <a href="#">DOANH NGHIỆP</a>
                <a href="#">HÀNH CHÍNH CÔNG</a>
            </nav>
            <div class="header-right">
                <button class="header-btn login">🔒 Đăng nhập</button>
                <div class="flag-btn"></div>
            </div>
        </div>
    </div>

    <div class="banner-wave"></div>

    <div class="report-container">
        <h1 class="page-title">BÁO CÁO CÔNG VIỆC</h1>

        <div class="report-grid">
            <div class="report-card">
                <label>Ngày báo cáo</label>
                <input type="date" x-model="form.entry_date">

                <label>Đơn vị</label>
                <select x-model="selectedArea" @change="loadOffices()">
                    <option value="">-- Chọn đơn vị --</option>
                    <template x-for="a in areas" :key="a.id">
                        <option :value="a.id" x-text="a.name"></option>
                    </template>
                </select>

                <label>Bưu cục</label>
                <select x-model="form.office_id">
                    <option value="">-- Chọn bưu cục --</option>
                    <template x-for="o in offices" :key="o.id">
                        <option :value="o.id" x-text="o.name"></option>
                    </template>
                </select>

                <label>Dịch vụ</label>
                <select x-model="form.service_id"> 
                    <option value="">-- Chọn dịch vụ --</option>
                    <template x-for="s in services" :key="s.id">
                        <option :value="s.id" x-text="s.name"></option>
                    </template>
                </select>
            </div>

            <div class="report-card">
                <!-- <label>Số lượng</label>
                <input type="number" step="1" x-model.number="form.quantity"> -->

                <!-- <label>Số tiền (VND)</label>
                <input type="number" step="0.01" x-model.number="form.amount"> -->

                <div class="dynamic-fields" x-show="selectedServiceConfig.length > 0">
                    <h3 class="dynamic-title">Chi tiết Dịch vụ</h3>
                    
                    <template x-for="field in selectedServiceConfig" :key="field.key">
                        <div>
                            <label x-text="field.label"></label>
                            <input 
                                :type="field.type" 
                                :step="field.type === 'number' ? '0.01' : ''" 
                                :placeholder="field.label"
                                x-model.number="form.DuLieuChiTiet[field.key]"
                            >
                        </div>
                    </template>
                </div>
                <!-- <label>Ghi chú</label>
                <textarea rows="4" x-model="form.note"></textarea> -->

                <div class="report-actions">
                    <button class="btn-submit" @click="save()" :disabled="saving">
                        <span x-show="!saving">Gửi báo cáo</span>
                        <span x-show="saving">Đang xử lý...</span>
                    </button>
                </div>

                <div x-show="message" x-text="message" class="message success"></div>
                <div x-show="error" x-text="error" class="message error"></div>
            </div>
        </div>
    </div>
    
<script>
    function reportPage() {
    return {
        // --- Trạng thái Dữ liệu ---
        areas: [],      // Danh sách Đơn vị Hành chính
        offices: [],    // Danh sách Bưu cục (đã lọc theo đơn vị)
        allOffices: [], // Danh sách tất cả Bưu cục (đã tải ban đầu)
        services: [],   // Danh sách Dịch vụ (có kèm CauHinhTruong)
        selectedServiceConfig: [], // Cấu hình trường động của dịch vụ được chọn

        // --- Trạng thái Form ---
        selectedArea: '', 
        form: {
            entry_date: new Date().toISOString().substring(0, 10),
            office_id: '',
            service_id: '',
            quantity: 0,
            amount: 0,
            note: '',
            DuLieuChiTiet: {}, // Lưu dữ liệu từ các trường động
        },

        // --- Trạng thái UI/API ---
        saving: false,
        message: '',
        error: '',
        
        // --- Khởi tạo (Lifecycle Hook) ---
        init() {
            this.loadInitialData();
            
            // Lắng nghe sự kiện thay đổi Dịch vụ (service_id) để tải cấu hình form động
            this.$watch('form.service_id', (serviceId) => {
                this.updateServiceConfig(serviceId);
            });
        },

        // --- Hàm Tải Dữ liệu Ban đầu từ API ---
        async loadInitialData() {
            try {
                // 1. Tải Đơn vị Hành chính
                const areaResponse = await fetch('/api/danh-muc/don-vi');
                this.areas = (await areaResponse.json()).map(a => ({ id: a.DonViID, name: a.TenDonVi }));

                // 2. Tải Tất cả Bưu cục 
                const officeResponse = await fetch('/api/danh-muc/buu-cuc');
                const officeData = await officeResponse.json();
                this.allOffices = officeData.map(o => ({ id: o.BuuCucID, name: o.TenBuuCuc, area_id: o.DonViID }));
                
                // 3. Tải Dịch vụ (Lưu kèm config)
                const serviceResponse = await fetch('/api/danh-muc/dich-vu');
                const serviceData = await serviceResponse.json();
                this.services = serviceData.map(s => ({ 
                    id: s.DichVuID, 
                    name: s.TenDichVu,
                    // Lưu cấu hình trường động từ CSDL
                    config: s.CauHinhTruong || [] 
                }));

            } catch (e) {
                this.error = 'Lỗi tải dữ liệu ban đầu. Vui lòng thử lại.';
                console.error('Lỗi tải dữ liệu:', e);
            }
        },
        
        // --- Hàm Cập nhật Cấu hình Trường Động ---
        updateServiceConfig(serviceId) {
            // Đặt lại các trạng thái
            this.selectedServiceConfig = [];
            this.form.DuLieuChiTiet = {}; 

            if (!serviceId) return;

            const selectedService = this.services.find(s => s.id == serviceId);
            
            if (selectedService && selectedService.config.length > 0) {
                this.selectedServiceConfig = selectedService.config;
                
                // Khởi tạo DuLieuChiTiet với các khóa cần thiết
                this.form.DuLieuChiTiet = selectedService.config.reduce((acc, field) => {
                    acc[field.key] = field.type === 'number' ? 0 : '';
                    return acc;
                }, {});
            }
        },

        // --- Hàm Lọc Bưu cục theo Đơn vị ---
        loadOffices() {
            this.form.office_id = ''; 
            // Lọc bưu cục dựa trên ID đơn vị được chọn
            this.offices = this.selectedArea ? this.allOffices.filter(o => o.area_id == this.selectedArea) : [];
        },

        // --- Hàm Helper: Tìm Tên từ ID ---
        getNameById(list, id) {
            // Sử dụng toUpperCase() để đảm bảo so sánh chuỗi (nếu ID là số, JS sẽ tự động chuyển đổi)
            const item = list.find(i => i.id == id);
            return item ? item.name : 'Không xác định';
        },
        
        // --- Hàm Gửi Báo cáo (SAVE) ---
        async save() {
            this.saving = true;
            this.message = '';
            this.error = '';

            // 1. Lấy Tên Đơn vị, Bưu cục, và Dịch vụ hiện tại
            const tenDonVi = this.getNameById(this.areas, this.selectedArea);
            const tenBuuCuc = this.getNameById(this.allOffices, this.form.office_id); // Dùng allOffices để tìm tên
            const tenDichVu = this.getNameById(this.services, this.form.service_id);

            // 2. Tạo đối tượng DuLieuChiTiet MỚI (Bao gồm cả thông tin danh mục)
            const chiTietBaoCao = {
                TenDonVi: tenDonVi,
                TenBuuCuc: tenBuuCuc,
                TenDichVu: tenDichVu,
                ...this.form.DuLieuChiTiet // Sao chép tất cả các trường động (Ngân hàng)
            };

            // 3. Chuẩn bị dữ liệu gửi đi (Data to be POSTed)
            const dataToSend = {
                BuuCucID: this.form.office_id,
                DichVuID: this.form.service_id,
                NgayBaoCao: this.form.entry_date,
                TongSoTien: parseFloat(this.form.amount || 0),
                SoLuong: parseInt(this.form.quantity || 0),
                GhiChu: this.form.note,
                
                // Gửi đối tượng DuLieuChiTiet đã được bổ sung
                DuLieuChiTiet: chiTietBaoCao, 
            };
            
            // Validation cơ bản
            if (!dataToSend.BuuCucID || !dataToSend.DichVuID) {
                this.error = 'Vui lòng chọn Đơn vị, Bưu cục và Dịch vụ.';
                this.saving = false;
                return;
            }

            try {
                const response = await fetch('/api/bao-cao', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
                    },
                    body: JSON.stringify(dataToSend)
                });

                const result = await response.json();

                if (response.ok) {
                    this.message = result.message || 'Gửi báo cáo thành công!';
                    this.resetForm(); 
                } else {
                    let errorMessage = 'Lỗi gửi báo cáo. ';
                    if (result.error) {
                        errorMessage += 'Chi tiết: ' + Object.values(result.error).join(', ');
                    } else if (result.message) {
                        errorMessage = result.message;
                    }
                    this.error = errorMessage;
                }

            } catch (e) {
                this.error = 'Lỗi kết nối mạng hoặc server không phản hồi.';
            } finally {
                this.saving = false;
            }
        },
        
        // --- Hàm đặt lại form ---
        resetForm() {
            this.form.office_id = '';
            this.form.service_id = '';
            this.form.quantity = 0;
            this.form.amount = 0;
            this.form.note = '';
            this.form.DuLieuChiTiet = {}; 
            this.selectedServiceConfig = []; 
            this.selectedArea = '';
            this.offices = [];
            this.message = '';
            this.error = '';
        }
    }
}
</script>
</body>
</html>