# Cấu hình Nhóm Khách Hàng (Customer Groups)

## Tổng quan

Menu "Nhóm khách hàng" đã được bổ sung vào sidebar cho Admin.

## ✅ Đã hoàn thành

### 1. **Routes**
**File**: `routes/web.php`

```php
// Import Controller
use App\Http\Controllers\Admin\CustomerGroupController;

// Routes
Route::prefix('admin/customer-groups')
    ->name('admin.customer-groups.')
    ->middleware(['auth', 'checkRole:1'])
    ->group(function () {
        Route::get('/', [CustomerGroupController::class, 'index'])->name('index');
        Route::get('/create', [CustomerGroupController::class, 'create'])->name('create');
        Route::post('/store', [CustomerGroupController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [CustomerGroupController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [CustomerGroupController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [CustomerGroupController::class, 'destroy'])->name('destroy');
    });
```

**Route names:**
- ✅ `admin.customer-groups.index` - Danh sách
- ✅ `admin.customer-groups.create` - Form tạo mới
- ✅ `admin.customer-groups.store` - Lưu mới
- ✅ `admin.customer-groups.edit` - Form sửa
- ✅ `admin.customer-groups.update` - Cập nhật
- ✅ `admin.customer-groups.destroy` - Xóa

---

### 2. **Controller**
**File**: `app/Http/Controllers/Admin/CustomerGroupController.php`

**Methods có sẵn:**
- ✅ `index()` - Danh sách nhóm
- ✅ `create()` - Form tạo mới
- ✅ `store()` - Lưu nhóm mới
- ✅ `edit($id)` - Form sửa
- ✅ `update($id)` - Cập nhật
- ✅ `destroy($id)` - Xóa

**Validation rules:**
```php
[
    'name' => 'required|unique:customer_groups,name|max:255',
    'description' => 'nullable|string',
]
```

---

### 3. **Views**
**Folder**: `resources/views/backend/customer_groups/`

**Files có sẵn:**
- ✅ `index.blade.php` - Danh sách nhóm khách hàng
- ✅ `create.blade.php` - Form tạo mới
- ✅ `edit.blade.php` - Form chỉnh sửa

---

### 4. **Sidebar Menu**
**File**: `resources/views/backend/layouts/partials/sidebar.blade.php`

**Vị trí**: Dòng 160-193

```blade
{{-- Quản lý khách hàng --}}
<li class="nav-item {{ $isCustomerManageActive ? 'active' : '' }}">
    <a data-bs-toggle="collapse" href="#customerMenu">
        <i class="fas fa-users"></i>
        <p>Quản lý khách hàng</p>
        <span class="caret"></span>
    </a>
    <div class="collapse {{ $isCustomerManageActive ? 'show' : '' }}" id="customerMenu">
        <ul class="nav nav-collapse">
            <li class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">
                <a href="{{ route('customers.index') }}">
                    <span class="sub-item">Danh sách khách hàng</span>
                </a>
            </li>
            @if (Route::has('admin.customer-groups.index'))
                <li class="{{ request()->routeIs('admin.customer-groups.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.customer-groups.index') }}">
                        <span class="sub-item">Nhóm khách hàng</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
</li>
```

**Active state logic:**
```php
$isCustomerManageActive = 
    request()->routeIs('customers.*') ||
    request()->routeIs('admin.customer-groups.*') ||
    request()->routeIs('admin.group-staff.*');
```

---

### 5. **Model**
**File**: `app/Models/CustomerGroup.php`

**Table**: `customer_groups`

**Columns:**
- `id` - Primary key
- `name` - Tên nhóm (unique, required)
- `description` - Mô tả (nullable)
- `is_active` - Trạng thái (boolean, default: 1)
- `created_at` - Thời gian tạo
- `updated_at` - Thời gian cập nhật

---

## 🎯 Cấu trúc Menu

```
Quản lý khách hàng
├── Danh sách khách hàng (/customers)
└── Nhóm khách hàng (/admin/customer-groups) ⭐ MỚI
```

---

## 🔐 Phân quyền

**Chỉ Admin (role = 1) mới có quyền:**
- ✅ Xem danh sách nhóm
- ✅ Tạo nhóm mới
- ✅ Sửa nhóm
- ✅ Xóa nhóm

**Middleware**: `checkRole:1`

---

## 📋 Tính năng

### Danh sách nhóm khách hàng
- Hiển thị tất cả nhóm
- Sắp xếp theo ID giảm dần
- Có nút thêm mới, sửa, xóa

### Tạo nhóm mới
- Form nhập tên nhóm (required, unique)
- Mô tả (optional)
- Trạng thái active/inactive
- Validation đầy đủ

### Chỉnh sửa nhóm
- Load thông tin nhóm hiện tại
- Cập nhật tên, mô tả, trạng thái
- Validation unique name (trừ chính nó)

### Xóa nhóm
- Xóa nhóm khách hàng
- Có thể cần kiểm tra ràng buộc với customers

---

## 🚀 Cách sử dụng

### 1. Truy cập menu
```
Admin Dashboard → Quản lý khách hàng → Nhóm khách hàng
```

### 2. URL
```
http://your-domain/admin/customer-groups
```

### 3. Tạo nhóm mới
```
1. Click "Thêm nhóm mới"
2. Nhập tên nhóm (bắt buộc)
3. Nhập mô tả (tùy chọn)
4. Chọn trạng thái
5. Click "Lưu"
```

### 4. Sửa nhóm
```
1. Click nút "Sửa" ở nhóm cần chỉnh sửa
2. Cập nhật thông tin
3. Click "Cập nhật"
```

### 5. Xóa nhóm
```
1. Click nút "Xóa" ở nhóm cần xóa
2. Xác nhận xóa
```

---

## 🔧 Kiểm tra

### Test routes
```bash
php artisan route:list --name=customer-groups
```

**Kết quả mong đợi:**
```
GET     /admin/customer-groups          admin.customer-groups.index
GET     /admin/customer-groups/create   admin.customer-groups.create
POST    /admin/customer-groups/store    admin.customer-groups.store
GET     /admin/customer-groups/edit/{id} admin.customer-groups.edit
PUT     /admin/customer-groups/update/{id} admin.customer-groups.update
DELETE  /admin/customer-groups/destroy/{id} admin.customer-groups.destroy
```

### Test menu hiển thị
1. Login với tài khoản Admin
2. Mở sidebar
3. Click "Quản lý khách hàng"
4. Kiểm tra có menu "Nhóm khách hàng"

### Test CRUD
1. ✅ Tạo nhóm mới
2. ✅ Xem danh sách
3. ✅ Sửa nhóm
4. ✅ Xóa nhóm

---

## 📝 Notes

- ✅ Routes đã được thêm vào `web.php`
- ✅ Controller đã tồn tại và hoạt động
- ✅ Views đã có sẵn
- ✅ Sidebar menu đã được cấu hình
- ✅ Middleware phân quyền đã được áp dụng
- ✅ Model và migration đã có

---

## 🎉 Kết luận

Menu "Nhóm khách hàng" đã được **BỔ SUNG THÀNH CÔNG** vào sidebar!

**Truy cập tại**: `Admin Dashboard → Quản lý khách hàng → Nhóm khách hàng`
