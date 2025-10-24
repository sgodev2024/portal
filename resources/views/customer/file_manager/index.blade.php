@extends('backend.layouts.master')

@section('title', 'Quản Lý File Của Tôi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-md-6">
            <h3><i class="fas fa-folder-open me-2"></i>Quản Lý File</h3>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('customer.file_manager.index') }}">
                            <i class="fas fa-home"></i> Thư mục gốc
                        </a>
                    </li>
                    @foreach($breadcrumb as $folder)
                        <li class="breadcrumb-item">
                            <a href="{{ route('customer.file_manager.index', ['folder' => $folder->id]) }}">
                                {{ $folder->name }}
                            </a>
                        </li>
                    @endforeach
                </ol>
            </nav>
        </div>
        <div class="col-md-6 text-end">
            <!-- Always allow creating a subfolder in the current folder (including root) -->
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createFolderModal">
                <i class="fas fa-folder-plus me-1"></i>Tạo thư mục
            </button>
            <button class="btn btn-success" onclick="document.getElementById('fileInput').click()">
                <i class="fas fa-upload me-1"></i>Upload File
            </button>
            <a href="{{ route('customer.file_manager.activities') }}" class="btn btn-info">
                <i class="fas fa-history me-1"></i>Lịch sử
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show">
            <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Storage Quota -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <div class="row align-items-center">
                <div class="col-md-9">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-hdd text-primary me-2 fs-4"></i>
                        <div class="flex-grow-1">
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar {{ $quota->used_percentage > 80 ? 'bg-danger' : 'bg-success' }}" 
                                     role="progressbar" 
                                     style="width: {{ $quota->used_percentage }}%">
                                    {{ $quota->used_percentage }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 text-end">
                    <strong>{{ $quota->used_space_formatted }}</strong> / {{ $quota->quota_limit_formatted }}
                </div>
            </div>
        </div>
    </div>

    <!-- Drag & Drop Upload Zone -->
    <div id="dropZone" class="border-3 border-dashed rounded p-5 mb-3 text-center" 
         style="border-color: #dee2e6; background: #f8f9fa;">
        <i class="fas fa-cloud-upload-alt text-muted" style="font-size: 3rem;"></i>
        <h5 class="mt-3">Kéo thả file vào đây để upload</h5>
        <p class="text-muted">hoặc click nút "Upload File" bên trên</p>
        <input type="file" id="fileInput" multiple style="display: none;">
    </div>

    <div class="row">
        <!-- Danh sách Folders -->
        @if($folders->count() > 0)
            <div class="col-12 mb-3">
                <h5><i class="fas fa-folder me-2"></i>Thư mục ({{ $folders->count() }})</h5>
                <div class="row">
                    @foreach($folders as $folder)
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <div class="card h-100 folder-item" onclick="openFolder({{ $folder->id }})">
                                <div class="card-body text-center p-3">
                                    <i class="fas fa-folder text-warning" style="font-size: 3rem;"></i>
                                    <h6 class="mt-2 mb-0 small text-truncate" title="{{ $folder->name }}">
                                        {{ Str::limit($folder->name, 20) }}
                                    </h6>
                                    
                                    @if(!$folder->is_root)
                                        <div class="dropdown position-absolute top-0 end-0 m-2">
                                            <button class="btn btn-sm btn-link text-muted" 
                                                    data-bs-toggle="dropdown" 
                                                    onclick="event.stopPropagation()">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item text-danger" 
                                                       href="#" 
                                                       onclick="event.stopPropagation(); deleteFolder({{ $folder->id }}, '{{ addslashes($folder->name) }}')">
                                                        <i class="fas fa-trash me-2"></i>Xóa
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Danh sách Files -->
        <div class="col-12">
            <h5><i class="fas fa-file me-2"></i>File ({{ $files->count() }})</h5>
            
            @if($files->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%"></th>
                                <th width="40%">Tên file</th>
                                <th width="15%">Kích thước</th>
                                <th width="20%">Ngày upload</th>
                                <th width="20%" class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($files as $file)
                                <tr>
                                    <td><i class="{{ $file->icon_class }} fs-4"></i></td>
                                    <td>
                                        <strong class="text-truncate d-inline-block" style="max-width: 300px;" title="{{ $file->original_name }}">
                                            {{ $file->original_name }}
                                        </strong>
                                        @if($file->description)
                                            <br><small class="text-muted">{{ $file->description }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $file->size_formatted }}</td>
                                    <td>{{ $file->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('customer.file_manager.download', $file->id) }}" 
                                               class="btn btn-success" 
                                               title="Tải xuống"
                                               data-bs-toggle="tooltip">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <button class="btn btn-info" 
                                                    title="Đổi tên"
                                                    data-bs-toggle="tooltip"
                                                    onclick="renameFile({{ $file->id }}, '{{ addslashes($file->original_name) }}')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-warning" 
                                                    title="Di chuyển"
                                                    data-bs-toggle="tooltip"
                                                    onclick="moveFile({{ $file->id }}, '{{ addslashes($file->original_name) }}')">
                                                <i class="fas fa-arrows-alt"></i>
                                            </button>
                                            <button class="btn btn-danger" 
                                                    title="Xóa"
                                                    data-bs-toggle="tooltip"
                                                    onclick="deleteFile({{ $file->id }}, '{{ addslashes($file->original_name) }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>Chưa có file nào trong thư mục này
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Tạo Thư Mục -->
<div class="modal fade" id="createFolderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('customer.file_manager.create_folder') }}" method="POST">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $currentFolder->id }}" required>
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-folder-plus me-2"></i>Tạo Thư Mục Mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên thư mục <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả (tùy chọn)</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check me-1"></i>Tạo thư mục
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Đổi Tên File -->
<div class="modal fade" id="renameModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="renameForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Đổi tên file</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Tên file mới</label>
                    <input type="text" id="renameInput" name="name" class="form-control" required>
                    <small class="text-muted">Không cần nhập phần mở rộng (.pdf, .docx, ...)</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check me-1"></i>Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Di Chuyển File -->
<div class="modal fade" id="moveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="moveForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Di chuyển file</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>File: <strong id="moveFileName"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Chọn thư mục đích</label>
                        <select name="folder_id" class="form-select" required>
                            <option value="{{ $rootFolder->id }}">📁 Thư mục gốc</option>
                            @foreach($allFolders ?? [] as $folder)
                                @if(!$folder->is_root)
                                    <option value="{{ $folder->id }}" {{ $folder->id == $currentFolder->id ? 'selected' : '' }}>
                                        {{ str_repeat('&nbsp;&nbsp;&nbsp;', substr_count($folder->path, '/') - 1) }}📁 {{ $folder->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check me-1"></i>Di chuyển
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form Delete -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('styles')
<style>
.folder-item {
    cursor: pointer;
    transition: all 0.3s;
    position: relative;
}

.folder-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
}

#dropZone {
    transition: all 0.3s ease;
}

#dropZone.dragover {
    background: #e7f3ff !important;
    border-color: #007bff !important;
    transform: scale(1.02);
}

.upload-progress {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 300px;
    z-index: 9999;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Responsive */
@media (max-width: 768px) {
    .btn-group-sm > .btn {
        padding: 0.25rem 0.4rem;
        font-size: 0.75rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Enable tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Drag & Drop Upload
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, preventDefaults, false);
    document.body.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    dropZone.addEventListener(eventName, () => dropZone.classList.add('dragover'), false);
});

['dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, () => dropZone.classList.remove('dragover'), false);
});

dropZone.addEventListener('drop', handleDrop, false);

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    uploadFiles(files);
}

fileInput.addEventListener('change', function() {
    uploadFiles(this.files);
    this.value = ''; // Reset input
});

function uploadFiles(files) {
    if (!files || files.length === 0) {
        return;
    }

    const currentFolderId = '{{ $currentFolder->id }}';
    
    if (!currentFolderId) {
        alert('Không xác định được thư mục hiện tại!');
        return;
    }

    const formData = new FormData();
    
    for (let file of files) {
        formData.append('files[]', file);
    }
    
    formData.append('folder_id', currentFolderId);
    formData.append('_token', '{{ csrf_token() }}');

    // Show progress
    const progressDiv = document.createElement('div');
    progressDiv.className = 'alert alert-info upload-progress';
    progressDiv.innerHTML = `
        <div class="d-flex align-items-center">
            <div class="spinner-border spinner-border-sm me-2"></div>
            <div>Đang upload ${files.length} file...</div>
        </div>
    `;
    document.body.appendChild(progressDiv);

    fetch('{{ route("customer.file_manager.upload") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        progressDiv.remove();
        if (data.success) {
            location.reload();
        } else {
            alert('Lỗi: ' + (data.error || 'Không xác định'));
        }
    })
    .catch(error => {
        progressDiv.remove();
        console.error('Upload error:', error);
        alert('Có lỗi xảy ra khi upload file!');
    });
}

function openFolder(id) {
    window.location.href = '{{ route("customer.file_manager.index") }}?folder=' + id;
}

function deleteFile(id, name) {
    if (confirm(`Bạn có chắc chắn muốn xóa file "${name}"?`)) {
        const form = document.getElementById('deleteForm');
        // Route is defined under /customer/file-manager/{id} (DELETE)
        form.action = `/customer/file-manager/${id}`;
        form.submit();
    }
}

function deleteFolder(id, name) {
    if (confirm(`Bạn có chắc chắn muốn xóa thư mục "${name}"?\n\nToàn bộ file và thư mục con sẽ bị xóa!`)) {
        const form = document.getElementById('deleteForm');
        form.action = `/customer/folders/${id}`;
        form.submit();
    }
}

function renameFile(id, currentName) {
    const modal = new bootstrap.Modal(document.getElementById('renameModal'));
    const form = document.getElementById('renameForm');
    const input = document.getElementById('renameInput');
    
    // Remove extension
    const lastDotIndex = currentName.lastIndexOf('.');
    const nameWithoutExt = lastDotIndex > 0 ? currentName.substring(0, lastDotIndex) : currentName;
    
    input.value = nameWithoutExt;
    // Route: POST /customer/file-manager/{id}/rename
    form.action = `/customer/file-manager/${id}/rename`;
    modal.show();
}

function moveFile(id, fileName) {
    const modal = new bootstrap.Modal(document.getElementById('moveModal'));
    const form = document.getElementById('moveForm');
    const fileNameDisplay = document.getElementById('moveFileName');
    
    fileNameDisplay.textContent = fileName;
    // Route: POST /customer/file-manager/{id}/move
    form.action = `/customer/file-manager/${id}/move`;
    modal.show();
}
</script>
@endpush
@endsection