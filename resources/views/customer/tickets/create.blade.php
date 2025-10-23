@extends('backend.layouts.master')

@section('title', 'Tạo Ticket Hỗ Trợ')

@section('content')
<div class="container-fluid py-4 py-lg-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white py-4">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-ticket-detailed fs-4"></i>
                        <div>
                            <h3 class="mb-0 fs-2">Tạo Ticket Hỗ Trợ</h3>
                            <small class="text-white-50">Liên hệ với đội ngũ hỗ trợ của chúng tôi</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4 p-lg-5">
                    <p class="text-muted mb-4 fs-5">
                        <i class="bi bi-info-circle me-2"></i>
                        Vui lòng mô tả chi tiết vấn đề của bạn, đội ngũ hỗ trợ sẽ phản hồi sớm nhất có thể.
                    </p>

                    {{-- Hiển thị lỗi --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5 class="alert-heading mb-3">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Lỗi nhập liệu
                            </h5>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li class="mb-2">{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Hiển thị thành công --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('customer.tickets.store') }}" method="POST" id="ticketForm">
                        @csrf

                        {{-- Tiêu đề --}}
                        <div class="mb-4">
                            <label for="subject" class="form-label fw-bold fs-5">
                                Tiêu đề <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="subject" 
                                name="subject" 
                                class="form-control form-control-lg @error('subject') is-invalid @enderror"
                                placeholder="VD: Không thể đăng nhập vào tài khoản"
                                value="{{ old('subject') }}"
                                required
                            >
                            @error('subject')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-2">Nhập tiêu đề ngắn gọn về vấn đề của bạn</small>
                        </div>

                        {{-- Danh mục và Mức độ ưu tiên --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="category" class="form-label fw-bold fs-5">
                                    Danh mục <span class="text-danger">*</span>
                                </label>
                                <select 
                                    class="form-select form-select-lg @error('category') is-invalid @enderror" 
                                    id="category" 
                                    name="category" 
                                    required
                                >
                                    <option value="">-- Chọn danh mục --</option>
                                    @foreach($categories as $key => $label)
                                        <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-2">Chọn danh mục phù hợp với vấn đề</small>
                            </div>

                            <div class="col-md-6">
                                <label for="priority" class="form-label fw-bold fs-5">
                                    Mức độ ưu tiên <span class="text-danger">*</span>
                                </label>
                                <select 
                                    class="form-select form-select-lg @error('priority') is-invalid @enderror" 
                                    id="priority" 
                                    name="priority" 
                                    required
                                >
                                    <option value="">-- Chọn mức độ --</option>
                                    @foreach($priorities as $key => $label)
                                        <option value="{{ $key }}" {{ old('priority', 'normal') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-2">Đánh giá mức độ khẩn cấp</small>
                            </div>
                        </div>

                        {{-- Hướng dẫn chọn mức độ ưu tiên --}}
                        <div class="alert alert-info mb-4">
                            <h6 class="alert-heading fw-bold">
                                <i class="bi bi-info-circle me-2"></i>Hướng dẫn chọn mức độ ưu tiên:
                            </h6>
                            <ul class="mb-0 small">
                                <li><strong>Khẩn cấp:</strong> Hệ thống bị sập, không thể hoạt động - Phản hồi trong 1-2 giờ</li>
                                <li><strong>Cao:</strong> Lỗi nghiêm trọng ảnh hưởng nhiều chức năng - Phản hồi trong 4-8 giờ</li>
                                <li><strong>Bình thường:</strong> Vấn đề thông thường cần hỗ trợ - Phản hồi trong 1 ngày</li>
                                <li><strong>Thấp:</strong> Câu hỏi, góp ý không khẩn cấp - Phản hồi trong 2-3 ngày</li>
                            </ul>
                        </div>

                        {{-- Mô tả chi tiết --}}
                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold fs-5">
                                Mô tả chi tiết <span class="text-danger">*</span>
                            </label>
                            <textarea 
                                id="description" 
                                name="description" 
                                class="form-control @error('description') is-invalid @enderror"
                                placeholder="Vui lòng mô tả chi tiết vấn đề của bạn, bao gồm các bước bạn đã thử..."
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-2">Càng chi tiết càng tốt để chúng tôi có thể giúp bạn nhanh hơn</small>
                        </div>

                        {{-- Tips --}}
                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">
                                    <i class="bi bi-lightbulb text-warning me-2"></i>Mẹo tạo ticket hiệu quả:
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="small mb-0">
                                            <li>Mô tả rõ ràng, cụ thể vấn đề</li>
                                            <li>Cung cấp các bước tái hiện lỗi</li>
                                            <li>Đính kèm ảnh chụp màn hình nếu có</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="small mb-0">
                                            <li>Ghi rõ thời gian xảy ra vấn đề</li>
                                            <li>Chọn đúng danh mục và mức độ</li>
                                            <li>Tránh tạo ticket trùng lặp</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Nút hành động --}}
                        <div class="d-flex flex-column flex-sm-row gap-3 pt-3">
                            <a href="{{ route('customer.tickets.index') }}" class="btn btn-secondary btn-lg flex-sm-grow-1">
                                <i class="bi bi-arrow-left-circle me-2"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg flex-sm-grow-1">
                                <i class="bi bi-send me-2"></i> Gửi Ticket
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CKEditor 5 CDN --}}
<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let editorInstance;

    ClassicEditor
        .create(document.querySelector('#description'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'underline', 'link', '|', 'bulletedList', 'numberedList', '|', 'blockQuote', 'undo', 'redo'],
        })
        .then(editor => {
            editorInstance = editor;
            console.log('CKEditor initialized');
        })
        .catch(error => {
            console.error('CKEditor error:', error);
        });

    // Validate và sync dữ liệu trước submit
    const form = document.querySelector('#ticketForm');
    form.addEventListener('submit', function(e) {
        if (editorInstance) {
            const data = editorInstance.getData().trim();
            if (!data) {
                e.preventDefault();
                alert('Vui lòng nhập mô tả chi tiết.');
                return;
            }
            document.querySelector('#description').value = data;
        }
    });

    // Hiển thị thông tin khi chọn category
    const categorySelect = document.getElementById('category');
    const prioritySelect = document.getElementById('priority');
    
    // Auto-suggest priority based on category
    categorySelect.addEventListener('change', function() {
        const suggestedPriority = {
            'technical': 'high',
            'billing': 'normal',
            'complaint': 'high',
            'general': 'normal',
            'feature_request': 'low',
            'other': 'normal'
        };
        
        const suggested = suggestedPriority[this.value];
        if (suggested && !prioritySelect.value) {
            prioritySelect.value = suggested;
        }
    });
});
</script>

<style>
.ck-editor__editable {
    min-height: 400px !important;
}

.form-select:focus,
.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
}
</style>
@endsection