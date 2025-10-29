# Hướng dẫn cập nhật trạng thái Ticket

## Các thay đổi đã thực hiện

### 1. Thay đổi trạng thái
- **Đã xóa**: 
  - `waiting_customer` (Đợi phản hồi)
  - `completed` (Hoàn tất)
  
- **Đã thêm**:
  - `responded` (Đã phản hồi)

### 2. Trạng thái mới
Hiện tại hệ thống có 4 trạng thái:
1. `new` - Mới tạo
2. `in_progress` - Đang xử lý
3. `responded` - Đã phản hồi (staff đã trả lời khách hàng)
4. `closed` - Đóng

### 3. Tự động đóng ticket
- Khi staff phản hồi ticket, trạng thái chuyển thành `responded` và lưu thời gian phản hồi vào `last_staff_response_at`
- Sau 3 ngày kể từ `last_staff_response_at`, nếu khách hàng không phản hồi lại, ticket sẽ tự động đóng
- Scheduled task chạy hàng ngày lúc 01:00 sáng

### 4. Luồng hoạt động mới
1. Khách hàng tạo ticket → `new`
2. Staff nhận ticket → `in_progress`
3. Staff phản hồi → `responded` (lưu `last_staff_response_at`)
4. Khách hàng phản hồi lại → `in_progress`
5. Staff phản hồi lại → `responded` (cập nhật `last_staff_response_at`)
6. Sau 3 ngày không có phản hồi từ khách → tự động `closed`

## Cách chạy migration

### Bước 1: Chạy migration
```bash
php artisan migrate
```

Migration sẽ tự động:
- Thêm cột `last_staff_response_at` vào bảng `tickets`
- Chuyển tất cả ticket có status `waiting_customer` thành `responded`
- Chuyển tất cả ticket có status `completed` thành `closed`

### Bước 2: Kiểm tra scheduled task
Đảm bảo cron job đang chạy:
```bash
php artisan schedule:work
```

Hoặc thêm vào crontab (production):
```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### Bước 3: Test thủ công (optional)
Để test scheduled task ngay lập tức:
```bash
php artisan tinker
```

Sau đó chạy:
```php
\App\Models\Ticket::where('status', \App\Models\Ticket::STATUS_RESPONDED)
    ->where('last_staff_response_at', '<=', now()->subDays(3))
    ->update(['status' => \App\Models\Ticket::STATUS_CLOSED]);
```

## Các file đã thay đổi

### Backend
1. `app/Models/Ticket.php` - Cập nhật constants và methods
2. `app/Console/Kernel.php` - Cập nhật scheduled task
3. `app/Http/Controllers/Admin/TicketController.php` - Cập nhật logic reply và xóa method complete
4. `app/Http/Controllers/Customer/TicketController.php` - Cập nhật logic reply
5. `routes/web.php` - Xóa route complete

### Frontend
1. `resources/views/backend/ticket/index.blade.php` - Cập nhật UI trạng thái
2. `resources/views/customer/tickets/index.blade.php` - Cập nhật icon
3. `resources/views/customer/tickets/show.blade.php` - Cập nhật statusMap

### Database
1. `database/migrations/2025_10_29_021400_update_ticket_statuses_and_add_last_staff_response_at.php` - Migration mới

## Lưu ý quan trọng

1. **Backup database** trước khi chạy migration
2. Tất cả ticket `waiting_customer` cũ sẽ được chuyển thành `responded`
3. Tất cả ticket `completed` cũ sẽ được chuyển thành `closed`
4. Các ticket `responded` cũ sẽ không có `last_staff_response_at`, cần staff phản hồi lại để cập nhật
5. Scheduled task chỉ tự động đóng ticket có `last_staff_response_at` sau 3 ngày

## Rollback (nếu cần)

Để rollback migration:
```bash
php artisan migrate:rollback
```

Migration rollback sẽ:
- Chuyển `responded` về `waiting_customer`
- Chuyển `closed` về `completed`
- Xóa cột `last_staff_response_at`
