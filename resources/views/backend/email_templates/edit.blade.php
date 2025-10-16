@extends('backend.layouts.master')
@section('title', 'Chỉnh sửa Email Template')

@section('content')
    <div class="container-fluid px-4 py-3">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-envelope me-2"></i>Chỉnh sửa Email Template</h5>
            </div>

            <div class="card-body">
                <form action="{{ route('admin.email_templates.update', $email_template->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Thông tin cơ bản -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tên template <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $email_template->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tiêu đề email <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror"
                                value="{{ old('subject', $email_template->subject) }}" required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Thông tin người gửi -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tên người gửi</label>
                            <input type="text" name="from_name"
                                class="form-control @error('from_name') is-invalid @enderror"
                                value="{{ old('from_name', $email_template->from_name) }}" placeholder="VD: Hệ thống ABC">
                            @error('from_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Nội dung Email -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nội dung Email (HTML) <span class="text-danger">*</span></label>
                        <textarea id="body_html" name="body_html" rows="15" class="form-control @error('body_html') is-invalid @enderror"
                            required>{{ old('body_html', $email_template->body_html) }}</textarea>
                        @error('body_html')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Trạng thái -->
                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                            {{ old('is_active', $email_template->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="is_active">
                            Kích hoạt template
                            <small class="text-muted d-block fw-normal">Template chỉ được sử dụng khi được kích hoạt</small>
                        </label>
                    </div>

                    <!-- Ghi chú: Biến có thể sử dụng -->
                    <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Ghi chú</h6>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="mb-0 small">
                                <li><code>{user_name}</code> - Tên người dùng</li>
                                <li><code>{new_password}</code> - Mật khẩu mới</li>
                                <li><code>{login_link}</code> - Link đăng nhập</li>
                            </ul>
                        </div>
                    </div>
                    <small class="text-muted d-block mt-2">
                        <i class="fas fa-lightbulb me-1"></i>
                        Sao chép và dán các biến trên vào nội dung email, chúng sẽ tự động được thay thế khi gửi email.
                    </small>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i> Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- CKEditor Script -->
    <script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/super-build/ckeditor.js"></script>

    <script>
        let editorInstance = null;

        document.addEventListener('DOMContentLoaded', function() {
            const editorElement = document.querySelector('#body_html');
            if (!editorElement) {
                console.error('Không tìm thấy phần tử #body_html');
                return;
            }

            // Khởi tạo CKEditor
            CKEDITOR.ClassicEditor.create(editorElement, {
                    toolbar: {
                        items: [
                            'undo', 'redo', '|',
                            'heading', '|',
                            'bold', 'italic', 'underline', 'strikethrough', '|',
                            'removeFormat', '|',
                            'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', '|',
                            'alignment', 'numberedList', 'bulletedList', '|',
                            'link', 'blockQuote', 'insertTable', '|',
                            'imageUpload', 'mediaEmbed'
                        ],
                        shouldNotGroupWhenFull: true
                    },
                    language: 'vi',
                    image: {
                        toolbar: [
                            'imageTextAlternative',
                            'toggleImageCaption',
                            'imageStyle:inline',
                            'imageStyle:block',
                            'imageStyle:side'
                        ]
                    },
                    table: {
                        contentToolbar: [
                            'tableColumn', 'tableRow', 'mergeTableCells',
                            'tableProperties', 'tableCellProperties'
                        ]
                    },
                    removePlugins: [
                        'CKBox', 'CKFinder', 'EasyImage',
                        'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges',
                        'RealTimeCollaborativeRevisionHistory', 'PresenceList', 'Comments',
                        'TrackChanges', 'TrackChangesData', 'RevisionHistory', 'Pagination',
                        'WProofreader', 'SlashCommand', 'Template', 'DocumentOutline',
                        'FormatPainter', 'CaseChange', 'TableOfContents',
                        'PasteFromOfficeEnhanced', 'AIAssistant', 'MultiLevelList'
                    ]
                })
                .then(editor => {
                    console.log('CKEditor đã khởi tạo thành công!', editor);
                    editorInstance = editor;
                    window.editor = editor;
                })
                .catch(error => {
                    console.error('Lỗi CKEditor:', error);
                });
        });
    </script>

@endsection
