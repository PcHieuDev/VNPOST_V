<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo cáo - Vietnam Post</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/report_styles.css') }}">
</head>
<body x-data="reportPage()">

<div class="report-alert-container" x-show="message || error" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-90" style="pointer-events: none;">
    <div x-show="message" class="report-alert alert-success" style="pointer-events: auto;">
        <p x-text="message"></p>
    </div>
    <div x-show="error" class="report-alert alert-error" style="pointer-events: auto;">
        <p x-text="error"></p>
    </div>
</div>

@include('layouts.header')

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

            <div class="dynamic-fields" x-show="selectedServiceConfig.length > 0">
                <h3 style="margin-bottom: 10px; font-size: 16px; color: #0066cc;">Chi tiết Dịch vụ</h3>

                <div class="dynamic-fields-grid">
                    <template x-for="field in selectedServiceConfig" :key="field.key">
                        <div>
                            <label x-text="field.label"></label>
                            <input
                                :type="field.type"
                                :step="field.type === 'number' ? 'any' : ''"
                                :placeholder="field.label"
                                x-model.number="form.DuLieuChiTiet[field.key]"
                            >
                        </div>
                    </template>
                </div>
            </div>

            <div x-show="!form.service_id" style="min-height: 150px; display: flex; align-items: center; justify-content: center; color: #888;">
                <p>Vui lòng chọn Dịch vụ để hiển thị các trường chi tiết.</p>
            </div>

            <div class="report-actions">
                <button type="button" class="btn-import" @click="$refs.fileInput.click()">
                    📥 Import Excel
                </button>
                <input
                    type="file"
                    accept=".xlsx, .xls"
                    x-ref="fileInput"
                    @change="handleFileChange($event)"
                    class="hidden-file-input"
                >
                <button class="btn-submit" @click="save()" :disabled="saving">
                    <span x-show="!saving">Gửi báo cáo</span>
                    <span x-show="saving">Đang xử lý...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/report_page.js') }}"></script>
</body>
</html>
