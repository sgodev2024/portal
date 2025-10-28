@extends('backend.layouts.master')

@section('title', 'Tạo Ticket Hỗ Trợ')

@section('content')
<div class="container-fluid py-4 notranslate">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            
            {{-- Page Header --}}
            <div class="mb-4">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('customer.tickets.index') }}">Tickets</a></li>
                        <li class="breadcrumb-item active">Tạo mới</li>
                    </ol>
                </nav>
                <h1 class="h3 fw-bold mb-2">
                    <i class="bi bi-ticket-detailed text-primary me-2"></i>
                    Tạo Ticket Hỗ Trợ
                </h1>
                <p class="text-muted mb-0">Mô tả chi tiết vấn đề của bạn để nhận được hỗ trợ tốt nhất</p>
            </div>

            <div class="row g-4">
                {{-- Main Form --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            
                            {{-- Hiển thị lỗi --}}
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <div class="d-flex gap-2">
                                        <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                                        <div>
                                            <h6 class="alert-heading mb-2">Vui lòng kiểm tra lại thông tin</h6>
                                            <ul class="mb-0 small ps-3">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            {{-- Hiển thị thành công --}}
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form action="{{ route('customer.tickets.store') }}" method="POST" id="ticketForm">
                                @csrf

                                {{-- Tiêu đề --}}
                                <div class="mb-4">
                                    <label for="subject" class="form-label fw-semibold">
                                        Tiêu đề ticket <span class="text-danger">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="subject" 
                                        name="subject" 
                                        class="form-control form-control-lg @error('subject') is-invalid @enderror"
                                        placeholder="VD: Không thể đăng nhập vào tài khoản"
                                        value="{{ old('subject') }}"
                                        required
                                        maxlength="255"
                                    >
                                    @error('subject')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Nhập tiêu đề ngắn gọn, súc tích về vấn đề của bạn
                                    </div>
                                </div>

                                {{-- Danh mục và Mức độ ưu tiên --}}
                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label for="category" class="form-label fw-semibold">
                                            Danh mục <span class="text-danger">*</span>
                                        </label>
                                        <select 
                                            class="form-select @error('category') is-invalid @enderror" 
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
                                    </div>

                                    <div class="col-md-6">
                                        <label for="priority" class="form-label fw-semibold">
                                            Mức độ ưu tiên <span class="text-danger">*</span>
                                        </label>
                                        <select 
                                            class="form-select @error('priority') is-invalid @enderror" 
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
                                    </div>
                                </div>

                                {{-- Priority hint --}}
                                <div id="priority-hint" class="alert alert-light border mb-4" style="display: none;">
                                    <div class="d-flex gap-2">
                                        <i class="bi bi-lightbulb text-warning"></i>
                                        <small id="priority-hint-text"></small>
                                    </div>
                                </div>

                                {{-- Mô tả chi tiết --}}
                                <div class="mb-4">
                                    <label for="description" class="form-label fw-semibold">
                                        Mô tả chi tiết <span class="text-danger">*</span>
                                    </label>
                                    <textarea 
                                        id="description" 
                                        name="description" 
                                        class="form-control @error('description') is-invalid @enderror"
                                        placeholder="Mô tả chi tiết vấn đề của bạn..."
                                    >{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Cung cấp càng nhiều thông tin càng tốt: bước tái hiện lỗi, thời gian xảy ra, ảnh chụp màn hình...
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="d-flex gap-3 pt-3 border-top">
                                    <a href="{{ route('customer.tickets.index') }}" class="btn btn-light btn-lg px-4">
                                        <i class="bi bi-x-lg me-2"></i>Hủy
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-lg px-4 flex-grow-1">
                                        <i class="bi bi-send me-2"></i>Gửi Ticket
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="col-lg-4">
                    
                    {{-- Priority Guide --}}
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-speedometer2 text-primary me-2"></i>
                                Hướng dẫn chọn mức độ ưu tiên
                            </h6>
                            <div class="priority-guide">
                                <div class="priority-item mb-3 pb-3 border-bottom">
                                    <div class="d-flex align-items-start gap-2">
                                        <span class="badge bg-danger">Khẩn cấp</span>
                                        <div class="small">
                                            <div class="fw-semibold mb-1">1-2 giờ phản hồi</div>
                                            <div class="text-muted">Hệ thống ngừng hoạt động, ảnh hưởng nghiêm trọng</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="priority-item mb-3 pb-3 border-bottom">
                                    <div class="d-flex align-items-start gap-2">
                                        <span class="badge bg-warning text-dark">Cao</span>
                                        <div class="small">
                                            <div class="fw-semibold mb-1">4-8 giờ phản hồi</div>
                                            <div class="text-muted">Lỗi nghiêm trọng ảnh hưởng nhiều chức năng</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="priority-item mb-3 pb-3 border-bottom">
                                    <div class="d-flex align-items-start gap-2">
                                        <span class="badge bg-info">Bình thường</span>
                                        <div class="small">
                                            <div class="fw-semibold mb-1">1 ngày làm việc</div>
                                            <div class="text-muted">Vấn đề thông thường cần hỗ trợ</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="priority-item">
                                    <div class="d-flex align-items-start gap-2">
                                        <span class="badge bg-secondary">Thấp</span>
                                        <div class="small">
                                            <div class="fw-semibold mb-1">2-3 ngày làm việc</div>
                                            <div class="text-muted">Câu hỏi, góp ý không khẩn cấp</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tips --}}
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-lightbulb text-warning me-2"></i>
                                Mẹo tạo ticket hiệu quả
                            </h6>
                            <ul class="list-unstyled mb-0 small">
                                <li class="mb-2 d-flex gap-2">
                                    <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                                    <span>Mô tả rõ ràng, cụ thể vấn đề gặp phải</span>
                                </li>
                                <li class="mb-2 d-flex gap-2">
                                    <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                                    <span>Cung cấp các bước để tái hiện lỗi</span>
                                </li>
                                <li class="mb-2 d-flex gap-2">
                                    <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                                    <span>Đính kèm ảnh chụp màn hình nếu có thể</span>
                                </li>
                                <li class="mb-2 d-flex gap-2">
                                    <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                                    <span>Ghi rõ thời gian xảy ra vấn đề</span>
                                </li>
                                <li class="mb-2 d-flex gap-2">
                                    <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                                    <span>Chọn đúng danh mục và mức độ ưu tiên</span>
                                </li>
                                <li class="d-flex gap-2">
                                    <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                                    <span>Kiểm tra kỹ trước khi gửi, tránh ticket trùng lặp</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

{{-- CKEditor 5 --}}
<script src="https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.umd.js"></script>
<link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.css" />

<script>
document.addEventListener('DOMContentLoaded', function() {
    const {
        ClassicEditor,
        Essentials,
        Bold,
        Italic,
        Underline,
        Strikethrough,
        Code,
        Paragraph,
        Heading,
        Link,
        AutoLink,
        List,
        BlockQuote,
        Indent,
        Undo
    } = CKEDITOR;

    let editorInstance;

    // Initialize CKEditor
    ClassicEditor
        .create(document.querySelector('#description'), {
            plugins: [
                Essentials, Bold, Italic, Underline, Strikethrough, Code,
                Paragraph, Heading, Link, AutoLink, List,
                BlockQuote, Indent, Undo
            ],
            toolbar: {
                items: [
                    'undo', 'redo', '|',
                    'heading', '|',
                    'bold', 'italic', 'underline', '|',
                    'link', 'bulletedList', 'numberedList', '|',
                    'outdent', 'indent', '|',
                    'blockQuote'
                ],
                shouldNotGroupWhenFull: true
            },
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                ]
            }
        })
        .then(editor => {
            editorInstance = editor;
            editor.editing.view.change(writer => {
                writer.setStyle('min-height', '300px', editor.editing.view.document.getRoot());
            });
        })
        .catch(error => {
            console.error('CKEditor error:', error);
        });

    // Form validation
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

    // Priority hints
    const prioritySelect = document.getElementById('priority');
    const priorityHint = document.getElementById('priority-hint');
    const priorityHintText = document.getElementById('priority-hint-text');
    
    const priorityHints = {
        'urgent': {
            text: 'Khẩn cấp: Sử dụng cho vấn đề nghiêm trọng ảnh hưởng đến toàn bộ hệ thống. Chúng tôi sẽ phản hồi trong vòng 1-2 giờ.',
            class: 'alert-danger'
        },
        'high': {
            text: 'Cao: Dành cho lỗi quan trọng ảnh hưởng đến nhiều chức năng. Thời gian phản hồi: 4-8 giờ.',
            class: 'alert-warning'
        },
        'normal': {
            text: 'Bình thường: Vấn đề thông thường cần hỗ trợ. Chúng tôi sẽ phản hồi trong vòng 1 ngày làm việc.',
            class: 'alert-info'
        },
        'low': {
            text: 'Thấp: Câu hỏi hoặc yêu cầu không khẩn cấp. Thời gian phản hồi: 2-3 ngày làm việc.',
            class: 'alert-secondary'
        }
    };

    prioritySelect.addEventListener('change', function() {
        const value = this.value;
        if (value && priorityHints[value]) {
            priorityHint.className = 'alert border mb-4 ' + priorityHints[value].class;
            priorityHintText.textContent = priorityHints[value].text;
            priorityHint.style.display = 'block';
        } else {
            priorityHint.style.display = 'none';
        }
    });

    // Auto-suggest priority based on category
    const categorySelect = document.getElementById('category');
    const suggestedPriority = {
        'technical': 'high',
        'billing': 'normal',
        'complaint': 'high',
        'general': 'normal',
        'feature_request': 'low',
        'other': 'normal'
    };
    
    categorySelect.addEventListener('change', function() {
        const suggested = suggestedPriority[this.value];
        if (suggested && !prioritySelect.value) {
            prioritySelect.value = suggested;
            prioritySelect.dispatchEvent(new Event('change'));
        }
    });

    // Character counter for subject
    const subjectInput = document.getElementById('subject');
    const maxLength = 255;
    
    subjectInput.addEventListener('input', function() {
        const remaining = maxLength - this.value.length;
        const formText = this.parentElement.querySelector('.form-text');
        if (remaining <= 50) {
            formText.innerHTML = `<i class="bi bi-info-circle me-1"></i> Còn ${remaining} ký tự`;
            formText.classList.add('text-warning');
        } else {
            formText.innerHTML = '<i class="bi bi-info-circle me-1"></i> Nhập tiêu đề ngắn gọn, súc tích về vấn đề của bạn';
            formText.classList.remove('text-warning');
        }
    });
});
</script>

<style>
/* CKEditor */
.ck-editor__editable {
    min-height: 300px !important;
    max-height: 500px;
    overflow-y: auto;
}

/* Form Controls */
.form-control:focus,
.form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
}

.form-control-lg {
    font-size: 1rem;
}

/* Priority Guide Hover */
.priority-item {
    transition: all 0.2s ease;
}

.priority-item:hover {
    transform: translateX(4px);
}

/* Card Hover */
.card {
    transition: all 0.2s ease;
}

/* Breadcrumb */
.breadcrumb {
    background: transparent;
    padding: 0;
    margin-bottom: 1rem;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    font-size: 1.2em;
}

/* Alerts */
.alert {
    border-radius: 8px;
}

/* Buttons */
.btn-lg {
    padding: 0.75rem 1.5rem;
    font-weight: 500;
}

/* Responsive */
@media (max-width: 991px) {
    .priority-guide,
    .card-body {
        padding: 1rem !important;
    }
}

/* Smooth transitions */
* {
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}
</style>
@endsection