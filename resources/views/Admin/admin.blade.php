<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo cáo Công việc - Vietnam Post</title>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f9fc;
            color: #333;
        }
        .header {
            background-color: #0066cc; /* VNP Blue */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .logo-icon {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }
        .logo-text {
            color: white;
            font-weight: 700;
            font-size: 1.25rem;
        }
        .main-nav a {
            padding: 1rem;
            color: white;
            transition: background-color 0.3s;
            border-radius: 0.5rem;
            font-weight: 600;
        }
        .main-nav a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .banner-wave {
            height: 10px;
            background: linear-gradient(90deg, #ffa500 50%, #f7f9fc 50%); /* Orange + BG */
        }
        .report-card, .table-card {
            background-color: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        label {
            display: block;
            margin-top: 0.75rem;
            margin-bottom: 0.25rem;
            font-weight: 600;
            color: #555;
            font-size: 0.9rem;
        }
        input, select {
            width: 100%;
            padding: 0.65rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fcfcfc;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        input:focus, select:focus {
            border-color: #0066cc;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
        }
        .btn-submit {
            background-color: #ffa500; /* VNP Orange */
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 700;
            transition: background-color 0.3s, transform 0.1s;
            box-shadow: 0 4px 6px rgba(255, 165, 0, 0.3);
        }
        .btn-submit:hover:not(:disabled) {
            background-color: #e69500;
            transform: translateY(-1px);
        }
        .btn-submit:disabled {
            background-color: #ccc;
            cursor: not-allowed;
            box-shadow: none;
        }
        .btn-import {
            background-color: #e0e7ff; /* Light Blue */
            color: #0066cc;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        .btn-import:hover {
            background-color: #c7d2fe;
        }
        .report-alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 400px;
        }
        .alert-success {
            background-color: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #842029;
            border: 1px solid #f5c2c7;
        }
        .report-alert-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 100;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .dynamic-fields-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
        }
        .report-table th, .report-table td {
            padding: 0.75rem 1rem;
            text-align: left;
        }
        .report-table th {
            background-color: #eef2ff;
            color: #1e3a8a;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
        }
        .report-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .action-btn {
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: opacity 0.3s;
        }
        .action-btn:hover {
            opacity: 0.8;
        }
        .btn-edit { background-color: #0066cc; color: white; margin-right: 0.5rem; }
        .btn-delete { background-color: #ef4444; color: white; }
    </style>
</head>
<body x-data="reportPage()" x-init="init()">

<!-- Alert Container -->
<div class="report-alert-container" x-show="message || error" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-90" style="pointer-events: none;">
    <div x-show="message" class="report-alert alert-success" style="pointer-events: auto;">
        <p x-text="message"></p>
    </div>
    <div x-show="error" class="report-alert alert-error" style="pointer-events: auto;">
        <p x-text="error"></p>
    </div>
</div>

<!-- Header -->
<div class="header">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
        <div class="logo flex items-center">
            <img class="logo-icon rounded-full" src="https://placehold.co/40x40/0066cc/ffffff?text=VNP" alt="Vietnam Post Logo">
            <div class="logo-text">VIETNAM POST</div>
        </div>
        <nav class="main-nav hidden md:flex items-center space-x-4">
            <a href="#" style="color: #ffa500;">BÁO CÁO</a>
            <a href="#">DOANH NGHIỆP</a>
            <a href="#">HÀNH CHÍNH CÔNG</a>
        </nav>
        <div class="header-right flex items-center space-x-3">
            <button class="header-btn login text-white hover:text-yellow-400 transition" @click="showMessage('Đăng xuất thành công!', 'success')">
                🔓 Đăng xuất
            </button>
            <div class="w-6 h-6 bg-white rounded-full flex items-center justify-center text-xs font-bold text-gray-800">VN</div>
        </div>
    </div>
</div>
<div class="banner-wave"></div>

<div class="report-container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="page-title text-3xl font-bold text-center mb-6 text-gray-800">BÁO CÁO CÔNG VIỆC</h1>

    <!-- Report Entry Form (Create/Update) -->
    <div class="report-grid grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
        <!-- Input Fields Column -->
        <div class="report-card">
            <h2 class="text-xl font-semibold mb-4 text-[#0066cc]" x-text="editingId ? 'Cập nhật Báo cáo' : 'Nhập Báo cáo Mới'"></h2>

            <label>Ngày báo cáo</label>
            <input type="date" x-model="form.entry_date" class="mb-3">

            <label>Đơn vị</label>
            <select x-model="selectedArea" @change="loadOffices()" class="mb-3">
                <option value="">-- Chọn đơn vị --</option>
                <template x-for="a in areas" :key="a.id">
                    <option :value="a.id" x-text="a.name"></option>
                </template>
            </select>

            <label>Bưu cục</label>
            <select x-model="form.office_id" class="mb-3">
                <option value="">-- Chọn bưu cục --</option>
                <template x-for="o in offices" :key="o.id">
                    <option :value="o.id" x-text="o.name"></option>
                </template>
            </select>

            <label>Dịch vụ</label>
            <select x-model="form.service_id" class="mb-3" :disabled="editingId">
                <option value="">-- Chọn dịch vụ --</option>
                <template x-for="s in services" :key="s.id">
                    <option :value="s.id" x-text="s.name"></option>
                </template>
            </select>
        </div>

        <!-- Dynamic Fields & Actions Column -->
        <div class="report-card flex flex-col justify-between">
            <div>
                <div class="dynamic-fields" x-show="selectedServiceConfig.length > 0">
                    <h3 class="mb-3 font-semibold text-base text-[#0066cc]">Chi tiết Dịch vụ</h3>
                    <div class="dynamic-fields-grid">
                        <template x-for="field in selectedServiceConfig" :key="field.key">
                            <div>
                                <label x-text="field.label"></label>
                                <input
                                    :type="field.type"
                                    :step="field.type === 'number' ? 'any' : ''"
                                    :placeholder="field.label"
                                    x-model.number="form.DuLieuChiTiet[field.key]"
                                    class="mb-3"
                                >
                            </div>
                        </template>
                    </div>
                </div>

                <div x-show="!form.service_id" class="min-h-[150px] flex items-center justify-center text-gray-500 bg-gray-50 border border-dashed border-gray-200 rounded-lg p-4">
                    <p>Vui lòng chọn **Dịch vụ** để hiển thị các trường chi tiết.</p>
                </div>
            </div>

            <div class="report-actions flex flex-col sm:flex-row gap-3 mt-6 pt-4 border-t border-gray-100">
                <button type="button" class="btn-import flex-1" @click="$refs.fileInput.click()">
                    📥 Import Excel
                </button>
                <input
                    type="file"
                    accept=".xlsx, .xls"
                    x-ref="fileInput"
                    @change="handleFileChange($event)"
                    class="hidden"
                >

                <button type="button" class="btn-submit flex-1" @click="save()" :disabled="!isFormValid || saving">
                    <span x-show="!saving" x-text="editingId ? 'Cập nhật Báo cáo' : 'Gửi báo cáo'"></span>
                    <span x-show="saving">Đang xử lý...</span>
                </button>

                <button type="button" class="btn-import" x-show="editingId" @click="cancelEdit()">
                    Hủy
                </button>
            </div>
        </div>
    </div>
    
    <div class="table-card mt-10">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-2">Danh sách Báo cáo Đã gửi</h2>

        <!-- Report List (Read) -->
        <div class="overflow-x-auto">
            <template x-if="reports.length === 0">
                <p class="text-center text-gray-500 py-8">Chưa có báo cáo nào được gửi.</p>
            </template>
            
            <template x-if="reports.length > 0">
                <table class="report-table w-full border-collapse rounded-lg overflow-hidden">
                    <thead>
                        <tr>
                            <th class="w-1/12">ID</th>
                            <th class="w-2/12">Ngày</th>
                            <th class="w-2/12">Đơn vị</th>
                            <th class="w-2/12">Bưu cục</th>
                            <th class="w-3/12">Dịch vụ</th>
                            <th class="w-2/12">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="report in reports" :key="report.id">
                            <tr class="hover:bg-blue-50 transition duration-150">
                                <td x-text="report.id.substring(0, 4) + '...'"></td>
                                <td x-text="report.entry_date"></td>
                                <td x-text="getAreaName(report.area_id)"></td>
                                <td x-text="getOfficeName(report.office_id)"></td>
                                <td>
                                    <span x-text="getServiceName(report.service_id)" class="font-medium text-[#0066cc]"></span>
                                    <template x-if="Object.keys(report.DuLieuChiTiet).length > 0">
                                        <p class="text-xs text-gray-600 mt-1">
                                            Chi tiết: 
                                            <template x-for="(value, key) in report.DuLieuChiTiet" :key="key">
                                                <span class="mr-2" x-text="`${key}: ${formatNumber(value)}`"></span>
                                            </template>
                                        </p>
                                    </template>
                                </td>
                                <td>
                                    <button class="action-btn btn-edit" @click="editReport(report)">
                                        Sửa
                                    </button>
                                    <button class="action-btn btn-delete" @click="showDeleteConfirmation(report.id)">
                                        Xóa
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </template>
        </div>
    </div>

</div>

<!-- Custom Modal for Delete Confirmation -->
<div x-show="showModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50" x-transition.opacity>
    <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-sm" @click.away="showModal = false">
        <h3 class="text-xl font-bold mb-4 text-red-600">Xác nhận Xóa Báo cáo</h3>
        <p class="mb-6 text-gray-700">Bạn có chắc chắn muốn xóa báo cáo này không? Hành động này không thể hoàn tác.</p>
        <div class="flex justify-end space-x-3">
            <button @click="showModal = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-medium">
                Hủy
            </button>
            <button @click="confirmDelete()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                Xác nhận Xóa
            </button>
        </div>
    </div>
</div>


<script>
    function reportPage() {
        return {
            // --- STATE MANAGEMENT ---
            
            // Core Data
            reports: JSON.parse(localStorage.getItem('vnp_reports') || '[]'),
            areas: [
                { id: 1, name: 'Văn phòng Tổng Công ty' },
                { id: 2, name: 'Bưu điện TP. Hà Nội' },
                { id: 3, name: 'Bưu điện TP. HCM' },
            ],
            officesData: {
                1: [{ id: 101, name: 'Văn phòng Kế hoạch' }, { id: 102, name: 'Ban Tài chính' }],
                2: [{ id: 201, name: 'Bưu cục Đống Đa' }, { id: 202, name: 'Bưu cục Hoàn Kiếm' }],
                3: [{ id: 301, name: 'Bưu cục Quận 1' }, { id: 302, name: 'Bưu cục Tân Bình' }],
            },
            services: [
                { id: 'EMS', name: 'Chuyển phát nhanh (EMS)', config: [{ key: 'so_luong_bc', label: 'Số lượng Bưu gửi', type: 'number' }, { key: 'doanh_thu', label: 'Doanh thu (VND)', type: 'number' }] },
                { id: 'COD', name: 'Thu hộ (COD)', config: [{ key: 'so_don', label: 'Số đơn COD', type: 'number' }, { key: 'tong_tien_thu', label: 'Tổng tiền Thu hộ (VND)', type: 'number' }] },
                { id: 'FIN', name: 'Dịch vụ Tài chính', config: [{ key: 'so_giao_dich', label: 'Số giao dịch', type: 'number' }, { key: 'phi_dich_vu', label: 'Phí dịch vụ (VND)', type: 'number' }, { key: 'ty_le_hoan_thanh', label: 'Tỷ lệ hoàn thành (%)', type: 'number' }] },
            ],

            // Form State
            form: {
                entry_date: new Date().toISOString().substring(0, 10),
                office_id: '',
                service_id: '',
                DuLieuChiTiet: {},
                area_id: '', // Added to form to save it
            },
            selectedArea: '',
            offices: [],
            
            // UI State
            saving: false,
            message: '',
            error: '',
            editingId: null, // ID of the report being edited (null for creation)
            showModal: false, // For delete confirmation
            deletingId: null, // ID of the report to be deleted

            // --- COMPUTED / GETTERS ---

            get selectedServiceConfig() {
                const service = this.services.find(s => s.id === this.form.service_id);
                return service ? service.config : [];
            },

            get isFormValid() {
                // Check if all primary fields are filled
                if (!this.form.entry_date || !this.selectedArea || !this.form.office_id || !this.form.service_id) {
                    return false;
                }
                // Check if all dynamic fields are filled and are valid numbers (if type is number)
                for (const field of this.selectedServiceConfig) {
                    const value = this.form.DuLieuChiTiet[field.key];
                    if (value === null || value === undefined || value === '') {
                        return false;
                    }
                    if (field.type === 'number' && isNaN(Number(value))) {
                        return false;
                    }
                }
                return true;
            },

            // --- METHODS ---

            init() {
                // Initialize selectedArea from a previous form value if available (e.g. if reports exist)
                this.selectedArea = this.areas[0]?.id || '';
                this.form.area_id = this.selectedArea;
                this.loadOffices();
            },

            loadOffices() {
                // Reset office_id and dynamic data when area changes
                this.form.office_id = '';
                this.form.area_id = this.selectedArea;
                this.offices = this.officesData[this.selectedArea] || [];
            },

            resetForm() {
                this.form.entry_date = new Date().toISOString().substring(0, 10);
                this.form.office_id = '';
                this.form.service_id = '';
                this.form.area_id = '';
                this.selectedArea = '';
                this.offices = [];
                this.form.DuLieuChiTiet = {};
                this.editingId = null; // Important: reset editing state
                this.init(); // Re-initialize default values
            },

            showMessage(text, type = 'success') {
                if (type === 'success') {
                    this.message = text;
                    this.error = '';
                } else {
                    this.error = text;
                    this.message = '';
                }
                setTimeout(() => {
                    this.message = '';
                    this.error = '';
                }, 3000);
            },

            // --- CRUD OPERATIONS ---

            save() {
                if (!this.isFormValid) {
                    this.showMessage('Vui lòng điền đầy đủ và chính xác tất cả các trường dữ liệu.', 'error');
                    return;
                }

                this.saving = true;

                // Ensure numeric fields are correctly typed
                const serviceConfig = this.selectedServiceConfig;
                const detailData = {};
                for (const field of serviceConfig) {
                    detailData[field.key] = field.type === 'number' ? Number(this.form.DuLieuChiTiet[field.key]) : this.form.DuLieuChiTiet[field.key];
                }

                const newReport = {
                    ...this.form,
                    area_id: this.selectedArea, // Use selectedArea for area_id
                    DuLieuChiTiet: detailData,
                };

                setTimeout(() => { // Simulate API call delay
                    if (this.editingId) {
                        // UPDATE LOGIC
                        const index = this.reports.findIndex(r => r.id === this.editingId);
                        if (index !== -1) {
                            // Service ID must not change during editing, so we spread the old ID
                            this.reports[index] = { ...newReport, id: this.editingId };
                            this.showMessage('Cập nhật báo cáo thành công!');
                        }
                    } else {
                        // CREATE LOGIC
                        newReport.id = crypto.randomUUID(); // Simple unique ID generation
                        this.reports.push(newReport);
                        this.showMessage('Gửi báo cáo thành công!');
                    }

                    // Save to mock storage
                    localStorage.setItem('vnp_reports', JSON.stringify(this.reports));
                    
                    this.saving = false;
                    this.resetForm(); // Reset the form after save/update

                }, 800);
            },

            editReport(report) {
                // Set editing state
                this.editingId = report.id;
                
                // Load primary data
                this.form.entry_date = report.entry_date;
                this.selectedArea = report.area_id;
                this.form.service_id = report.service_id;
                
                // Must call loadOffices before setting office_id because offices array depends on selectedArea
                this.loadOffices(); 
                this.form.office_id = report.office_id;
                
                // Load dynamic data, ensuring it is a fresh copy
                this.form.DuLieuChiTiet = JSON.parse(JSON.stringify(report.DuLieuChiTiet));
                
                this.showMessage(`Đang chỉnh sửa báo cáo ID: ${report.id.substring(0, 4)}...`, 'info');
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            cancelEdit() {
                this.resetForm();
                this.showMessage('Đã hủy chỉnh sửa.');
            },
            
            showDeleteConfirmation(id) {
                this.deletingId = id;
                this.showModal = true;
            },

            confirmDelete() {
                if (this.deletingId) {
                    this.deleteReport(this.deletingId);
                    this.deletingId = null;
                }
                this.showModal = false;
            },

            deleteReport(id) {
                this.reports = this.reports.filter(r => r.id !== id);
                localStorage.setItem('vnp_reports', JSON.stringify(this.reports));
                this.showMessage('Xóa báo cáo thành công!');
            },
            
            // --- HELPER FUNCTIONS ---

            getAreaName(id) {
                return this.areas.find(a => a.id === id)?.name || 'N/A';
            },
            
            getOfficeName(id) {
                 // Check if the office exists in any area's data
                 for (const areaId in this.officesData) {
                    const office = this.officesData[areaId].find(o => o.id === id);
                    if (office) return office.name;
                }
                return 'N/A';
            },

            getServiceName(id) {
                return this.services.find(s => s.id === id)?.name || 'N/A';
            },
            
            formatNumber(value) {
                if (typeof value === 'number') {
                    // Simple VN format with commas
                    return value.toLocaleString('vi-VN');
                }
                return value;
            },

            handleFileChange(event) {
                const file = event.target.files[0];
                if (!file) {
                    return;
                }

                if (!this.form.service_id) {
                    this.showMessage('Vui lòng chọn Dịch vụ trước khi Import Excel.', 'error');
                    event.target.value = ''; // Reset file input
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    try {
                        const data = new Uint8Array(e.target.result);
                        const workbook = XLSX.read(data, { type: 'array' });
                        const sheetName = workbook.SheetNames[0];
                        const worksheet = workbook.Sheets[sheetName];
                        const json = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

                        // Assuming the first row is headers and the second row contains the data
                        if (json.length < 2) {
                            this.showMessage('Tệp Excel không có đủ dữ liệu (yêu cầu ít nhất 2 hàng).', 'error');
                            return;
                        }

                        const headers = json[0].map(h => String(h).trim().toLowerCase().replace(/\s/g, '_'));
                        const rowData = json[1];
                        const serviceConfigKeys = this.selectedServiceConfig.map(f => f.key);
                        let importedData = {};
                        let fieldsFound = 0;

                        // Map Excel columns to dynamic fields
                        serviceConfigKeys.forEach(key => {
                            const matchingIndex = headers.findIndex(h => h.includes(key.toLowerCase()));
                            if (matchingIndex !== -1 && rowData[matchingIndex] !== undefined) {
                                // Attempt to parse as number for number fields
                                const fieldDef = this.selectedServiceConfig.find(f => f.key === key);
                                let value = rowData[matchingIndex];

                                if (fieldDef.type === 'number') {
                                    value = Number(value);
                                    if (isNaN(value)) {
                                        this.showMessage(`Lỗi: Giá trị '${rowData[matchingIndex]}' cho trường ${fieldDef.label} không phải là số hợp lệ.`, 'error');
                                        return;
                                    }
                                }
                                importedData[key] = value;
                                fieldsFound++;
                            }
                        });

                        if (fieldsFound === serviceConfigKeys.length) {
                            this.form.DuLieuChiTiet = importedData;
                            this.showMessage('Import Excel thành công! Dữ liệu đã được điền vào form chi tiết.', 'success');
                        } else {
                            this.showMessage('Lỗi Import: Không tìm thấy tất cả các cột dữ liệu chi tiết bắt buộc trong tệp Excel.', 'error');
                        }
                    } catch (e) {
                        console.error('Lỗi khi xử lý file Excel:', e);
                        this.showMessage('Đã xảy ra lỗi khi đọc tệp Excel. Vui lòng kiểm tra định dạng.', 'error');
                    }
                };
                reader.readAsArrayBuffer(file);
                event.target.value = ''; // Reset file input
            },
        }
    }
</script>
</body>
</html>
