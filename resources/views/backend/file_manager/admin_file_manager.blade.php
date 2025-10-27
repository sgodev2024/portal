@extends('backend.layouts.master')

@section('title', 'File Manager - Tất cả Users')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h3><i class="fas fa-folder-open me-2"></i>File Manager</h3>
                <p class="text-muted">Quản lý file từ tất cả người dùng</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('admin.file_manager.download_history') }}" class="btn btn-info">
                    <i class="fas fa-history me-1"></i>Lịch sử tải
                </a>
            </div>
        </div>

        <!-- Alerts -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.file_manager.index') }}" class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">
                            <i class="fas fa-user me-1"></i>Lọc theo User
                        </label>
                        <select name="user_id" class="form-select">
                            <option value="">-- Tất cả Users --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} - {{ $user->account_id ?? 'N/A' }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">
                            <i class="fas fa-search me-1"></i>Tìm kiếm
                        </label>
                        <input type="text" name="search" class="form-control" placeholder="Tên file..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i>Tìm kiếm
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Upload Section -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-cloud-upload-alt me-2"></i>Upload File cho User</h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i>
                            Thay vì chọn User trực tiếp, bạn có thể upload các file PDF được đặt tên theo <strong>account_id</strong> của khách
                            (ví dụ: <code>07.pdf</code> hoặc <code>7.pdf</code>). Hệ thống sẽ tự động tìm khách hàng theo account_id
                            (bỏ số 0 phía trước) và gửi file báo cáo đến khách đó.
                        </div>
                    </div>
                </div>

                <!-- Drop Zone -->
                    <div id="dropZone" class="border-3 border-dashed rounded p-5 text-center"
                        style="border-color: #dee2e6; background: #f8f9fa; cursor: pointer; transition: all 0.3s;">
                        <i class="fas fa-cloud-upload-alt text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 mb-2">Kéo thả file vào đây để upload</h5>
                        <p class="text-muted mb-3">Hỗ trợ file tối đa 50MB</p>
                        <button type="button" id="chooseFileBtn" class="btn btn-primary">
                            <i class="fas fa-folder-open me-1"></i>Chọn File
                        </button>
                        <input type="file" id="fileInput" accept=".pdf" multiple style="display: none;">

                        <!-- Selected files preview and actions -->
                        <div id="selectedFilesList" class="mt-3"></div>
                        <div id="queueActions" class="mt-2" style="display: none;">
                            <button type="button" id="startUploadBtn" class="btn btn-success btn-sm me-2">Bắt đầu upload</button>
                            <button type="button" id="clearQueueBtn" class="btn btn-outline-secondary btn-sm">Xóa danh sách</button>
                        </div>
                    </div>
            </div>
        </div>

        <!-- Folders & Files -->
        <div class="row">
            @if ($folders->count() > 0)
                <div class="col-12 mb-4">
                    <h5 class="mb-3">
                        <i class="fas fa-folder me-2"></i>Thư mục người dùng ({{ $folders->count() }})
                    </h5>
                    <div class="row">
                        @foreach ($folders as $folder)
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                                <div class="card folder-card h-100 position-relative"
                                    onclick="openFolder({{ $folder->user_id }})"
                                    style="cursor: pointer; transition: all 0.3s;">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fas fa-folder" style="font-size: 3rem; color: #ffc107;"></i>
                                        </div>
                                        <h6 class="card-title text-truncate" title="{{ $folder->user->name }}">
                                            {{ Str::limit($folder->user->name, 20) }}
                                        </h6>
                                        <small class="text-muted d-block">
                                            <i class="fas fa-phone me-1"></i>{{ $folder->user->account_id ?? 'N/A' }}
                                        </small>
                                        <small class="text-muted d-block">
                                            <i class="fas fa-file me-1"></i>{{ $folder->files()->count() }} file
                                        </small>
                                    </div>
                                    <div class="card-footer bg-light text-center">
                                        <small class="text-muted">
                                            <i class="fas fa-lock me-1"></i>Hệ thống
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Files List -->
            <div class="col-12">
                <h5 class="mb-3">
                    <i class="fas fa-file me-2"></i>Danh sách File ({{ $files->total() }})
                </h5>

                @if ($files->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="5%">Icon</th>
                                    <th width="30%">Tên File</th>
                                    <th width="15%">User</th>
                                    <th width="12%">Kích thước</th>
                                    <th width="15%">Ngày upload</th>
                                    <th width="18%" class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($files as $index => $file)
                                    <tr>
                                        <td>{{ $files->firstItem() + $index }}</td>
                                        <td>
                                            <i class="{{ $file->icon_class }} fs-5"></i>
                                        </td>
                                        <td>
                                            <strong class="text-truncate d-block" style="max-width: 250px;"
                                                title="{{ $file->original_name }}">
                                                {{ $file->original_name }}
                                            </strong>
                                            @if ($file->description)
                                                <small class="text-muted">{{ Str::limit($file->description, 40) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $file->user->name }}</div>
                                            <small class="text-muted">
                                                <i class="fas fa-phone me-1"></i>{{ $file->user->account_id ?? 'N/A' }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="fw-semibold">{{ $file->size_formatted }}</span>
                                        </td>
                                        <td>
                                            <small>{{ $file->created_at->format('d/m/Y H:i') }}</small>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('admin.file_manager.show_file', $file->id) }}"
                                                    class="btn btn-info" title="Xem chi tiết" data-bs-toggle="tooltip">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.file_manager.download', $file->id) }}"
                                                    class="btn btn-success" title="Tải xuống" data-bs-toggle="tooltip">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger" title="Xóa"
                                                    data-bs-toggle="tooltip"
                                                    onclick="deleteFile({{ $file->id }}, '{{ addslashes($file->original_name) }}', '{{ $file->user->name }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $files->links() }}
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>Không có file nào
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Delete Form -->
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Enable tooltips
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });

                // Hover effect cho folder card
                document.querySelectorAll('.folder-card').forEach(card => {
                    card.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateY(-5px)';
                        this.style.boxShadow = '0 0.5rem 1rem rgba(0,0,0,0.15)';
                    });
                    card.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateY(0)';
                        this.style.boxShadow = 'none';
                    });
                });
            });

            function openFolder(userId) {
                window.location.href = '{{ route('admin.file_manager.index') }}?user_id=' + userId;
            }

            function deleteFile(id, fileName, userName) {
                if (confirm(
                        `Bạn có chắc chắn muốn xóa file "${fileName}" của user "${userName}"?\n\nHành động này không thể hoàn tác!`
                    )) {
                    const form = document.getElementById('deleteForm');
                    form.action = `{{ route('admin.file_manager.delete_file', '') }}/${id}`;
                    form.submit();
                }
            }

            // ========== DRAG & DROP UPLOAD ==========
            const dropZone = document.getElementById('dropZone');
            const fileInput = document.getElementById('fileInput');
            const chooseFileBtn = document.getElementById('chooseFileBtn');
            const selectedFilesList = document.getElementById('selectedFilesList');
            const startUploadBtn = document.getElementById('startUploadBtn');
            const clearQueueBtn = document.getElementById('clearQueueBtn');
            const queueActions = document.getElementById('queueActions');

            // queuedFiles will hold File objects selected by admin before upload
            let queuedFiles = [];

            if (dropZone) {
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, preventDefaults, false);
                    document.body.addEventListener(eventName, preventDefaults, false);
                });

                function preventDefaults(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }

                ['dragenter', 'dragover'].forEach(eventName => {
                    dropZone.addEventListener(eventName, () => {
                        dropZone.style.background = '#e7f3ff';
                        dropZone.style.borderColor = '#007bff';
                        dropZone.style.transform = 'scale(1.02)';
                    }, false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, () => {
                        dropZone.style.background = '#f8f9fa';
                        dropZone.style.borderColor = '#dee2e6';
                        dropZone.style.transform = 'scale(1)';
                    }, false);
                });

                dropZone.addEventListener('drop', handleDrop, false);

                function handleDrop(e) {
                    console.log('dropZone: drop event', e);
                    const dt = e.dataTransfer;
                    const files = dt.files;
                    console.log('Files dropped:', files);
                    try {
                        addFilesToQueue(files);
                    } catch (err) {
                        console.error('Error adding files to queue from drop:', err);
                        showErrorMessage('Lỗi JS khi thêm file: ' + err.message);
                    }
                }

                // open file dialog
                // open file dialog
                chooseFileBtn.addEventListener('click', function() {
                    console.log('chooseFileBtn clicked');
                    fileInput.click();
                });

                fileInput.addEventListener('change', function() {
                    console.log('fileInput: change, files selected:', this.files);
                    try {
                        addFilesToQueue(this.files);
                    } catch (err) {
                        console.error('Error adding files to queue from input:', err);
                        showErrorMessage('Lỗi JS khi thêm file: ' + err.message);
                    }
                    this.value = '';
                });

                // start upload button
                startUploadBtn.addEventListener('click', function() {
                    console.log('startUploadBtn clicked, queuedFiles:', queuedFiles.map(f => f.name));
                    uploadFiles(queuedFiles);
                });

                clearQueueBtn.addEventListener('click', function() {
                    queuedFiles = [];
                    renderSelectedFiles();
                });
            }

            // Add a FileList or File[] to the queue and update UI
            function addFilesToQueue(files) {
                console.log('addFilesToQueue called, files:', files);
                if (!files || files.length === 0) {
                    console.warn('addFilesToQueue: no files');
                    return;
                }

                // Convert FileList to array and push (avoid duplicates by name+size)
                const incoming = Array.from(files);
                for (let f of incoming) {
                    const exists = queuedFiles.some(q => q.name === f.name && q.size === f.size);
                    if (!exists) queuedFiles.push(f);
                }

                renderSelectedFiles();

                // Automatically start upload after files are added (previous behavior expected automatic upload)
                // Use a next-tick to avoid re-entrancy issues
                setTimeout(() => {
                    try {
                        console.log('Auto-starting upload for queued files:', queuedFiles.map(f => f.name));
                        uploadFiles(queuedFiles);
                    } catch (err) {
                        console.error('Error during automatic upload start:', err);
                        showErrorMessage('Lỗi khi bắt đầu upload: ' + err.message);
                    }
                }, 50);
            }

            function renderSelectedFiles() {
                if (!selectedFilesList) return;

                if (queuedFiles.length === 0) {
                    selectedFilesList.innerHTML = '<div class="text-muted">Chưa có file nào được chọn</div>';
                    queueActions.style.display = 'none';
                    return;
                }

                let html = '<ul class="list-group">';
                queuedFiles.forEach((file, idx) => {
                    let icon = 'file-alt';
                    const name = file.name.toLowerCase();
                    if (name.endsWith('.pdf')) icon = 'file-pdf';
                    else if (name.match(/\.(doc|docx)$/)) icon = 'file-word';
                    else if (name.match(/\.(xls|xlsx)$/)) icon = 'file-excel';
                    else if (name.match(/\.(zip|rar)$/)) icon = 'file-archive';

                    html += `<li class="list-group-item d-flex justify-content-between align-items-center p-2">
                                <div>
                                    <i class="fas fa-${icon} text-primary me-2"></i>
                                    <strong>${file.name}</strong>
                                    <small class="text-muted ms-2">(${formatFileSize(file.size)})</small>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeQueuedFile(${idx})">Xóa</button>
                                </div>
                             </li>`;
                });
                html += '</ul>';

                selectedFilesList.innerHTML = html;
                queueActions.style.display = 'block';
            }

            // Utility: format file size ( bytes -> human readable )
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // exposed for inline onclick
            function removeQueuedFile(index) {
                queuedFiles.splice(index, 1);
                renderSelectedFiles();
            }

            function uploadFiles(files) {
                console.log('uploadFiles invoked with', files);
                if (!files || files.length === 0) {
                    showErrorMessage('Không có file để upload.');
                    return;
                }

                showUploadProgress(files.length);

                const formData = new FormData();
                for (let file of files) {
                    formData.append('files[]', file);
                }
                formData.append('_token', '{{ csrf_token() }}');

                console.log('Uploading files:', queuedFiles.map(f => f.name));
                fetch('{{ route('admin.file_manager.upload') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        // keep status for debugging
                        console.log('Upload response status:', response.status, response.statusText);
                        if (!response.ok) {
                            // try to read text for a useful error message
                            return response.text().then(text => {
                                console.error('Upload failed, server returned:', text);
                                hideUploadProgress();
                                showErrorMessage('Lỗi server khi upload: ' + (text ? text.substring(0, 200) : response.statusText));
                                throw new Error('Upload failed: ' + response.status);
                            });
                        }
                        return response.json().catch(() => {
                            // OK but not JSON
                            return null;
                        });
                    })
                    .then(data => {
                        hideUploadProgress();
                        if (data === null) {
                            // OK response but no JSON (fallback)
                            console.warn('Upload succeeded but server returned non-JSON response');
                            showSuccessMessage(`Đã upload ${files.length} file thành công (server trả về HTML).`);
                            queuedFiles = [];
                            renderSelectedFiles();
                            setTimeout(() => location.reload(), 1200);
                            return;
                        }

                        if (data.success) {
                            showSuccessMessage(data.message || `Đã upload ${files.length} file thành công!`);
                            if (Array.isArray(data.files) && data.files.length > 0) {
                                let resultsHtml = '<div class="mt-2">';
                                data.files.forEach(f => {
                                    const url = f.download_url || '#';
                                    resultsHtml += `<div><a href="${url}" target="_blank">${f.file_name}</a></div>`;
                                });
                                resultsHtml += '</div>';
                                const popup = document.createElement('div');
                                popup.className = 'alert alert-light position-fixed top-0 start-50 translate-middle-x mt-5';
                                popup.style.zIndex = 10000;
                                popup.innerHTML = `<strong>Kết quả:</strong> ${resultsHtml}`;
                                document.body.appendChild(popup);
                                setTimeout(() => popup.remove(), 6000);
                            }
                            queuedFiles = [];
                            renderSelectedFiles();
                            setTimeout(() => location.reload(), 1200);
                        } else {
                            const errs = data.errors || [data.error || 'Lỗi không xác định'];
                            showErrorMessage(errs.join('\n'));
                        }
                    })
                    .catch(error => {
                        // error already shown above in non-ok branch; ensure progress hidden
                        hideUploadProgress();
                        console.error('Upload exception:', error);
                    });
            }

            function showUploadProgress(fileCount) {
                const progressDiv = document.createElement('div');
                progressDiv.id = 'uploadProgress';
                progressDiv.className = 'position-fixed bottom-0 end-0 m-3';
                progressDiv.style.zIndex = '9999';
                progressDiv.innerHTML = `
        <div class="card shadow-lg">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div>
                        <small class="d-block fw-semibold">Đang upload ${fileCount} file...</small>
                        <div class="progress" style="width: 200px; height: 5px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated w-100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
                document.body.appendChild(progressDiv);
            }

            function hideUploadProgress() {
                const progress = document.getElementById('uploadProgress');
                if (progress) {
                    progress.remove();
                }
            }

            function showSuccessMessage(message) {
                const alert = document.createElement('div');
                alert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3';
                alert.style.zIndex = '10000';
                alert.innerHTML = `
        <i class="fas fa-check-circle me-2"></i>${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
                document.body.appendChild(alert);
                setTimeout(() => alert.remove(), 3000);
            }

            function showErrorMessage(message) {
                const alert = document.createElement('div');
                alert.className = 'alert alert-danger alert-dismissible fade show position-fixed top-0 end-0 m-3';
                alert.style.zIndex = '10000';
                alert.innerHTML = `
        <i class="fas fa-exclamation-circle me-2"></i>${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
                document.body.appendChild(alert);
                setTimeout(() => alert.remove(), 4000);
            }
        </script>
    @endpush
@endsection