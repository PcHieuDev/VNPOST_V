function reportPage() {
    return {
        // --- Trạng thái Dữ liệu ---
        areas: [],
        offices: [],
        allOffices: [],
        services: [],
        selectedServiceConfig: [],

        // --- Trạng thái Form ---
        selectedArea: '',
        form: {
            // Khởi tạo ngày ở định dạng YYYY-MM-DD (chuẩn ISO/HTML Date)
            entry_date: new Date().toISOString().substring(0, 10),
            office_id: '',
            service_id: '',
            quantity: 0,
            amount: 0,
            note: '',
            DuLieuChiTiet: {},
        },

        // --- Trạng thái UI/API ---
        saving: false,
        message: '',
        error: '',
        // TRẠNG THÁI MỚI: Lưu dữ liệu Excel tạm thời chờ cấu hình
        pendingExcelData: null,

        // --- Khởi tạo (Lifecycle Hook) ---
        init() {
            this.loadInitialData();

            // Lắng nghe sự kiện thay đổi Dịch vụ (service_id)
            this.$watch('form.service_id', (serviceId) => {
                this.updateServiceConfig(serviceId);
            });
        },

        // ----------------------------------------------------------------
        // --- HÀM TẢI DỮ LIỆU DANH MỤC ---
        // ----------------------------------------------------------------
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

        // ----------------------------------------------------------------
        // --- HÀM XỬ LÝ CẤU HÌNH VÀ SỬA LỖI TIMING ---
        // ----------------------------------------------------------------
        updateServiceConfig(serviceId) {
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

            // LOGIC SỬA LỖI TIMING: KIỂM TRA DỮ LIỆU EXCEL ĐANG CHỜ
            if (this.pendingExcelData && this.pendingExcelData.serviceId == serviceId) {
                // Ánh xạ dữ liệu chi tiết ngay sau khi cấu hình đã được tải
                const { headers, values } = this.pendingExcelData;
                const dataMap = {};

                headers.forEach((header, index) => {
                    dataMap[header] = values[index];
                });

                this.mapDynamicFields(headers, values, serviceId, dataMap);
                this.pendingExcelData = null; // Xóa dữ liệu chờ sau khi xử lý xong
            }
        },

        // ----------------------------------------------------------------
        // --- HÀM HELPER VÀ XỬ LÝ LỌC BƯU CỤC ---
        // ----------------------------------------------------------------
        loadOffices() {
            this.form.office_id = '';
            this.offices = this.selectedArea ? this.allOffices.filter(o => o.area_id == this.selectedArea) : [];
        },

        // --- Helper: Tìm ID từ Tên ---
        getIdByName(list, name) {
            const item = list.find(i => i.name.trim() === name.trim());
            return item ? item.id : null;
        },

        // --- Helper: Tìm Tên từ ID ---
        getNameById(list, id) {
            const item = list.find(i => i.id == id);
            return item ? item.name : 'Không xác định';
        },

        // --- Helper: Định dạng ngày từ Excel (dd/mm/yyyy hoặc dd-mm-yyyy) sang YYYY-MM-DD ---
        formatDateFromExcel(dateValue) {
            if (!dateValue) return new Date().toISOString().substring(0, 10);

            const dateString = String(dateValue).trim();
            const separator = dateString.includes('/') ? '/' : (dateString.includes('-') ? '-' : null);

            if (separator) {
                const parts = dateString.split(separator);
                if (parts.length === 3) {
                    const day = parts[0].padStart(2, '0');
                    const month = parts[1].padStart(2, '0');
                    const year = parts[2];

                    // Trả về YYYY-MM-DD (format chuẩn cho HTML input type="date")
                    return `${year}-${month}-${day}`;
                }
            }

            // Trường hợp ngày đã ở format ISO hoặc không hợp lệ, trả về mặc định
            if (dateString.match(/^\d{4}-\d{2}-\d{2}$/)) {
                return dateString;
            }
            return new Date().toISOString().substring(0, 10);
        },

        // ----------------------------------------------------------------
        // --- XỬ LÝ IMPORT EXCEL ---
        // ----------------------------------------------------------------
        handleFileChange(event) {
            this.message = '';
            this.error = '';
            const file = event.target.files[0];
            if (file) {
                this.saving = true;
                this.pendingExcelData = null; // Đảm bảo reset trạng thái chờ
                const reader = new FileReader();

                reader.onload = (e) => {
                    try {
                        const data = new Uint8Array(e.target.result);
                        const workbook = XLSX.read(data, { type: 'array' });
                        const sheetName = workbook.SheetNames[0];
                        const worksheet = workbook.Sheets[sheetName];
                        const json = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

                        this.processExcelData(json);
                        this.message = 'Dữ liệu Excel đã được tải. Vui lòng kiểm tra và nhấn Gửi báo cáo.';
                    } catch (e) {
                        this.error = 'Lỗi đọc file Excel. Đảm bảo file không bị hỏng hoặc cấu trúc sai.';
                        console.error("Lỗi đọc Excel:", e);
                    } finally {
                        this.saving = false;
                        event.target.value = ''; // Reset input file
                    }
                };
                reader.readAsArrayBuffer(file);
            }
        },

        // --- Hàm Ánh xạ dữ liệu Excel vào Form ---
        processExcelData(excelData) {
            if (excelData.length < 2) {
                this.error = 'File Excel không có đủ dữ liệu.';
                return;
            }

            const headers = excelData[0].map(h => h ? String(h).trim() : '');
            const values = excelData[1];

            const data = {};
            headers.forEach((header, index) => {
                data[header] = values[index];
            });

            // 1. Xử lý DỊCH VỤ
            const tenDichVu = (data['Dịch vụ'] || '').trim();
            const selectedService = this.services.find(s => s.name.trim() === tenDichVu);
            if (!selectedService) {
                this.error = `Dịch vụ "${tenDichVu}" không tồn tại.`;
                return;
            }

            // 2. Xử lý ĐƠN VỊ & BƯU CỤC
            const tenDonVi = (data['Tên Đơn vị'] || '').trim();
            const tenBuuCuc = (data['Tên Bưu cục'] || '').trim();

            this.selectedArea = this.getIdByName(this.areas, tenDonVi);
            if (!this.selectedArea) { this.error = `Đơn vị "${tenDonVi}" không tồn tại.`; return; }

            this.loadOffices();

            this.form.office_id = this.getIdByName(this.allOffices, tenBuuCuc);
            if (!this.form.office_id) { this.error = `Bưu cục "${tenBuuCuc}" không tồn tại.`; return; }

            // 3. Xử lý NGÀY BÁO CÁO (ĐÃ SỬA LỖI HIỂN THỊ NGÀY)
            this.form.entry_date = this.formatDateFromExcel(data['Ngày']);

            // 4. Reset các trường không sử dụng
            this.form.quantity = 0;
            this.form.amount = 0;
            this.form.note = '';

            // LOGIC QUAN TRỌNG: LƯU TRỮ DỮ LIỆU EXCEL ĐANG CHỜ ÁNH XẠ
            this.pendingExcelData = { headers, values, serviceId: selectedService.id };

            // Kích hoạt watcher (sẽ gọi updateServiceConfig)
            this.form.service_id = selectedService.id;
        },

        // --- Hàm ánh xạ chi tiết ---
        mapDynamicFields(headers, values, serviceId, dataMap) {
            let fieldsFound = false;

            if (!this.selectedServiceConfig || this.selectedServiceConfig.length === 0) {
                return false;
            }

            this.selectedServiceConfig.forEach(fieldConfig => {
                const headerLabel = fieldConfig.label.trim();
                const dynamicKey = fieldConfig.key;

                let excelValue = dataMap[headerLabel];

                if (excelValue !== undefined && excelValue !== null) {
                    fieldsFound = true;
                    const fieldType = fieldConfig.type;

                    this.form.DuLieuChiTiet[dynamicKey] = fieldType === 'number'
                        ? parseFloat(excelValue) || 0
                        : String(excelValue);
                } else {
                    // Khởi tạo giá trị 0 hoặc rỗng nếu không tìm thấy trong Excel
                    this.form.DuLieuChiTiet[dynamicKey] = fieldConfig.type === 'number' ? 0 : '';
                }
            });

            return fieldsFound;
        },

        // --- Helper: TÍNH TỔNG SỐ TIỀN TỪ CÁC TRƯỜNG NUMBER TRONG DuLieuChiTiet ---
        calcTongSoTien() {
            if (!this.selectedServiceConfig || this.selectedServiceConfig.length === 0) {
                return 0;
            }

            let sum = 0;
            this.selectedServiceConfig.forEach(fieldConfig => {
                if (fieldConfig.type === 'number') {
                    const key = fieldConfig.key;
                    const rawVal = this.form.DuLieuChiTiet[key];
                    const numVal = parseFloat(rawVal);
                    if (!isNaN(numVal)) {
                        sum += numVal;
                    }
                }
            });

            return sum;
        },

        // ----------------------------------------------------------------
        // --- Hàm Gửi Báo cáo (SAVE) ---
        // ----------------------------------------------------------------
        async save() {
            this.saving = true;
            this.message = '';
            this.error = '';

            const tenDonVi  = this.getNameById(this.areas, this.selectedArea);
            const tenBuuCuc = this.getNameById(this.allOffices, this.form.office_id);
            const tenDichVu = this.getNameById(this.services, this.form.service_id);

            const chiTietBaoCao = {
                TenDonVi:   tenDonVi,
                TenBuuCuc:  tenBuuCuc,
                TenDichVu:  tenDichVu,
                ...this.form.DuLieuChiTiet
            };

            // TÍNH TỔNG TỰ ĐỘNG TỪ CẤU HÌNH TRƯỜNG SỐ
            const tongSoTienTuDong = this.calcTongSoTien();

            const dataToSend = {
                BuuCucID:    this.form.office_id,
                DichVuID:    this.form.service_id,
                NgayBaoCao:  this.form.entry_date,

                // TongSoTien giờ được tính tự động
                TongSoTien:  tongSoTienTuDong,

                // SoLuong: giữ nguyên logic cũ
                SoLuong:     parseInt(this.form.quantity || 0),

                GhiChu:      this.form.note,
                DuLieuChiTiet: chiTietBaoCao,
            };

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
                    this.message = "Bạn đã báo cáo thành công! 🎉";
                    this.resetForm();

                    // Tự động ẩn thông báo thành công sau 5 giây
                    setTimeout(() => {
                        this.message = '';
                    }, 5000);

                } else {
                    let errorMessage = 'Lỗi gửi báo cáo. ';
                    if (result.error) {
                        errorMessage += 'Chi tiết: ' + Object.values(result.error).join(', ');
                    } else if (result.message) {
                        errorMessage = result.message;
                    }
                    this.error = errorMessage;

                    // Tự động ẩn lỗi sau 7 giây
                    setTimeout(() => {
                        this.error = '';
                    }, 7000);
                }

            } catch (e) {
                this.error = 'Lỗi kết nối mạng hoặc server không phản hồi.';
                setTimeout(() => {
                    this.error = '';
                }, 7000);
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
        },
    }
}
