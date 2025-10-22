@extends('backend.layouts.master')

@section('title', 'Chi tiết Báo cáo')

@section('content')
    <style>
        .report-page {
            background: #f8f9fa;
            min-height: 100vh;
            padding: 2rem 0;
        }

        .breadcrumb-custom {
            background: white;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(28, 21, 153, 0.1);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn-back {
            background: #0511ad;
            color: white;
            border: none;
            padding: 0.5rem 1.25rem;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: #180daf;
            color: white;
            transform: translateX(-3px);
        }

        .breadcrumb-text {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .report-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

      
        .file-icon-wrapper {
            width: 70px;
            height: 70px;
            background: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .file-icon-wrapper i {
            font-size: 2rem;
            color: #495057;
        }

        .report-title {
            color: white;
            font-size: 1.875rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .report-description {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
            line-height: 1.6;
        }

        .content-section {
            padding: 2.5rem;
        }

        .info-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 2rem;
            height: 100%;
            border: 1px solid #dee2e6;
        }

        .info-card-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.75rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #dee2e6;
        }

        .info-icon {
            width: 42px;
            height: 42px;
            background: #495057;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .info-card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #212529;
            margin: 0;
        }

        .info-row {
            display: flex;
            padding: 1.1rem 0;
            border-bottom: 1px solid #e9ecef;
        }

        .info-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .info-row:first-child {
            padding-top: 0;
        }

        .info-label {
            flex: 0 0 150px;
            font-weight: 600;
            color: #495057;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
        }

        .info-value {
            flex: 1;
            color: #212529;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
        }

        .file-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 1rem;
            background: #495057;
            color: white;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .action-section {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 1px solid #dee2e6;
            position: sticky;
            top: 2rem;
        }

        .action-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid #e9ecef;
        }

        .action-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #212529;
            margin-bottom: 0.5rem;
        }

        .action-subtitle {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .download-primary {
            background: #28a745;
            border: none;
            color: white;
            padding: 1.25rem;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 700;
            width: 100%;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            text-decoration: none;
        }

        .download-primary:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(40, 167, 69, 0.4);
            color: white;
        }

        .download-primary i {
            font-size: 1.3rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .stat-item {
            background: #f8f9fa;
            padding: 1.25rem;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #dee2e6;
        }

        .stat-icon {
            font-size: 1.75rem;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #6c757d;
            margin-bottom: 0.25rem;
            font-weight: 500;
        }

        .stat-value {
            font-size: 1rem;
            font-weight: 700;
            color: #212529;
        }

        .alert-warning-custom {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-left: 4px solid #ffc107;
            border-radius: 8px;
            padding: 1.25rem;
            margin-top: 1.5rem;
        }

        .alert-warning-custom .alert-icon {
            font-size: 1.5rem;
            color: #856404;
        }

        .alert-warning-custom .alert-text {
            color: #856404;
            font-weight: 600;
            font-size: 0.9rem;
            margin: 0;
            line-height: 1.5;
        }

        .security-badge {
            background: #d4edda;
            border: 1px solid #28a745;
            border-radius: 8px;
            padding: 0.85rem 1rem;
            margin-top: 1.5rem;
            text-align: center;
        }

        .security-badge i {
            color: #155724;
            margin-right: 0.5rem;
            font-size: 1rem;
        }

        .security-badge span {
            color: #155724;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .divider {
            height: 1px;
            background: #dee2e6;
            margin: 1.5rem 0;
        }
    </style>

    <div class="report-page">
        <div class="container-fluid px-4">
            <!-- Breadcrumb -->
            <div class="breadcrumb-custom">
                <a href="{{ route('customer.files.reports') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    <span>Quay lại</span>
                </a>
                <span class="breadcrumb-text">
                    <i class="fas fa-folder"></i> Danh sách báo cáo / Chi tiết
                </span>
            </div>

            <!-- Main Content -->
            <div class="report-container">
                <!-- Header Section -->
          

                <!-- Content Section -->
                <div class="content-section">
                    <div class="row g-4">
                        <!-- Info Section -->
                        <div class="col-lg-8">
                            <div class="info-card">
                                <div class="info-card-header">
                                    <div class="info-icon">
                                        <i class="fas fa-file-invoice"></i>
                                    </div>
                                    {{-- <h2 class="info-card-title">Thông tin chi tiết</h2> --}}
                                    <h2 class="info-card-title">{{ $file->title }}</h2>
                                    @if ($file->description)
                                        <p class="report-description">{{ $file->description }}</p>
                                    @endif
                                </div>

                                <div class="info-row">
                                    <div class="info-label">
                                        <i class="fas fa-file-alt" style="color: #007bff;"></i>
                                        Tên file
                                    </div>
                                    <div class="info-value">
                                        <strong>{{ $file->file_name }}</strong>
                                    </div>
                                </div>

                                <div class="info-row">
                                    <div class="info-label">
                                        <i class="fas fa-hdd" style="color: #17a2b8;"></i>
                                        Kích thước
                                    </div>
                                    <div class="info-value">
                                        <span class="file-badge">
                                            <i class="fas fa-database"></i>
                                            {{ $file->file_size_formatted }}
                                        </span>
                                    </div>
                                </div>

                                <div class="info-row">
                                    <div class="info-label">
                                        <i class="fas fa-file-code" style="color: #ffc107;"></i>
                                        Định dạng
                                    </div>
                                    <div class="info-value">
                                        <span class="file-badge">
                                            <i class="fas fa-tag"></i>
                                            {{ strtoupper($file->file_type) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="info-row">
                                    <div class="info-label">
                                        <i class="fas fa-user" style="color: #28a745;"></i>
                                        Gửi bởi
                                    </div>
                                    <div class="info-value">
                                        <i class="fas fa-user-circle" style="color: #007bff;"></i>
                                        <strong>{{ $file->uploader->name }}</strong>
                                    </div>
                                </div>

                                <div class="info-row">
                                    <div class="info-label">
                                        <i class="fas fa-clock" style="color: #dc3545;"></i>
                                        Ngày gửi
                                    </div>
                                    <div class="info-value">
                                        <i class="fas fa-calendar-check" style="color: #6c757d;"></i>
                                        <strong>{{ $file->sent_at->format('d/m/Y H:i') }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Section -->
                        <div class="col-lg-4">
                            <div class="action-section">
                                <div class="action-header">
                                    <h3 class="action-title">Tải xuống file</h3>
                                    <p class="action-subtitle">File báo cáo đã sẵn sàng</p>
                                </div>

                                <a href="{{ route('customer.files.download_report', $file->id) }}"
                                    class="download-primary">
                                    <i class="fas fa-cloud-download-alt"></i>
                                    <span>Tải xuống ngay</span>
                                </a>

                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
