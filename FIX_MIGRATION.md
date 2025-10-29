# Hướng dẫn sửa lỗi Migration

## Lỗi gặp phải
```
SQLSTATE[01000]: Warning: 1265 Data truncated for column 'status' at row 1
```

Lỗi này xảy ra vì cột `status` trong database là ENUM và giá trị `responded` chưa có trong danh sách ENUM.

## Giải pháp

Migration đã được cập nhật để xử lý đúng thứ tự:
1. Thêm giá trị mới `responded` vào ENUM (cùng với các giá trị cũ)
2. Chuyển đổi dữ liệu từ giá trị cũ sang mới
3. Xóa các giá trị cũ không dùng nữa khỏi ENUM
4. Thêm cột `last_staff_response_at`

## Cách khắc phục

### Bước 1: Rollback migration vừa chạy (nếu đã chạy)
```bash
php artisan migrate:rollback --step=1
```

### Bước 2: Chạy lại migration đã sửa
```bash
php artisan migrate
```

Migration sẽ tự động:
- ✅ Cập nhật ENUM để bao gồm cả `responded`
- ✅ Chuyển `waiting_customer` → `responded`
- ✅ Chuyển `completed` → `closed`
- ✅ Xóa `waiting_customer` và `completed` khỏi ENUM
- ✅ Thêm cột `last_staff_response_at`

### Bước 3: Kiểm tra
Sau khi migration thành công, kiểm tra:

```sql
-- Kiểm tra cấu trúc bảng
DESCRIBE tickets;

-- Kiểm tra ENUM values
SHOW COLUMNS FROM tickets WHERE Field = 'status';

-- Kiểm tra dữ liệu
SELECT status, COUNT(*) as count FROM tickets GROUP BY status;
```

Kết quả mong đợi cho ENUM:
```
'new', 'in_progress', 'responded', 'closed'
```

## Nếu vẫn gặp lỗi

### Option 1: Fresh migration (CHỈ dùng cho development)
```bash
php artisan migrate:fresh --seed
```
⚠️ **CẢNH BÁO**: Lệnh này sẽ XÓA TẤT CẢ dữ liệu!

### Option 2: Sửa thủ công bằng SQL
```sql
-- Thêm giá trị responded vào ENUM
ALTER TABLE tickets MODIFY COLUMN status 
ENUM('new', 'in_progress', 'completed', 'closed', 'waiting_customer', 'responded') 
DEFAULT 'new';

-- Cập nhật dữ liệu
UPDATE tickets SET status = 'responded' WHERE status = 'waiting_customer';
UPDATE tickets SET status = 'closed' WHERE status = 'completed';

-- Xóa giá trị cũ khỏi ENUM
ALTER TABLE tickets MODIFY COLUMN status 
ENUM('new', 'in_progress', 'responded', 'closed') 
DEFAULT 'new';

-- Thêm cột mới
ALTER TABLE tickets ADD COLUMN last_staff_response_at TIMESTAMP NULL AFTER status;
```

## Test sau khi sửa

1. Tạo ticket mới → kiểm tra status = `new`
2. Staff reply → kiểm tra status = `responded` và `last_staff_response_at` được set
3. Customer reply → kiểm tra status = `in_progress`
4. Staff reply lại → kiểm tra status = `responded` và `last_staff_response_at` được cập nhật

## Liên hệ
Nếu vẫn gặp vấn đề, kiểm tra log tại `storage/logs/laravel.log`
