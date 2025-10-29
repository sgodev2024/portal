# Kiểm tra Thông báo & Email cho Tickets

## Tổng quan

Hệ thống tickets đã được kiểm tra và cập nhật đầy đủ thông báo + email cho tất cả các trạng thái.

## ✅ Các trường hợp đã có đầy đủ thông báo

### 1. **Ticket mới được tạo**
**Trigger**: Customer tạo ticket mới

**Thông báo**:
- ✅ Gửi cho: Admin + Staff
- ✅ Tiêu đề: "Ticket mới #X: [subject]"
- ✅ Nội dung: "Khách hàng [name] vừa tạo ticket mới"

**Email**:
- ✅ Gửi cho: Customer
- ✅ Template: `ticket_created`
- ✅ Nội dung: Xác nhận ticket đã được tạo

**File**: `Customer\TicketController@store`
```php
TicketNotificationService::notifyNewTicket($ticket);
```

---

### 2. **Customer phản hồi ticket**
**Trigger**: Customer gửi reply

**Thông báo**:
- ✅ Gửi cho: Admin + Staff được gán (nếu có)
- ✅ Tiêu đề: "Khách phản hồi Ticket #X"
- ✅ Nội dung: "Khách hàng [name] vừa phản hồi ticket"

**Email**:
- ❌ Không gửi (không cần thiết)

**File**: `Customer\TicketController@reply`
```php
TicketNotificationService::notifyCustomerReply($ticket);
```

---

### 3. **Staff/Admin phản hồi ticket** → Chuyển sang "Đã phản hồi"
**Trigger**: Staff/Admin gửi reply

**Thông báo**:
- ✅ Gửi cho: Customer
- ✅ Tiêu đề: "Nhân viên phản hồi Ticket #X"
- ✅ Nội dung: "[Staff name] vừa phản hồi ticket của bạn"

**Email**:
- ✅ Gửi cho: Customer
- ✅ Template: `ticket_replied`
- ✅ Nội dung: Thông báo có phản hồi mới

**Trạng thái**:
- ✅ Chuyển sang: `responded`
- ✅ Cập nhật: `last_staff_response_at = now()`

**File**: `Admin\TicketController@reply`
```php
$ticket->update([
    'status' => Ticket::STATUS_RESPONDED,
    'last_staff_response_at' => now()
]);
TicketNotificationService::notifyStaffReply($ticket, $user);
```

---

### 4. **Admin gán ticket cho Staff**
**Trigger**: Admin assign ticket

**Thông báo**:
- ✅ Gửi cho: Customer (ticket đã được gán)
- ✅ Gửi cho: Staff (bạn được gán ticket)

**Email**:
- ✅ Gửi cho: Customer
- ✅ Template: `ticket_assigned`
- ✅ Gửi cho: Staff
- ✅ Template: `ticket_assigned_staff`

**File**: `Admin\TicketController@assign`
```php
TicketNotificationService::notifyTicketAssigned($ticket, $staff);
```

---

### 5. **Staff claim ticket**
**Trigger**: Staff tự nhận ticket

**Thông báo**:
- ✅ Gửi cho: Customer
- ✅ Tiêu đề: "Ticket #X đã được nhận xử lý"
- ✅ Nội dung: "Nhân viên [name] đã nhận xử lý ticket của bạn"

**Email**:
- ❌ Không gửi (tùy chọn)

**File**: `Admin\TicketController@claim`
```php
TicketNotificationService::notifyTicketClaimed($ticket, $user);
```

---

### 6. **Admin đóng ticket thủ công**
**Trigger**: Admin click "Đóng ticket"

**Thông báo**:
- ✅ Gửi cho: Customer
- ✅ Tiêu đề: "Ticket #X đã đóng"
- ✅ Nội dung: "Ticket đã được đóng"

**Email**:
- ✅ Gửi cho: Customer
- ✅ Template: `ticket_closed`

**File**: `Admin\TicketController@close`
```php
$ticket->update(['status' => Ticket::STATUS_CLOSED]);
TicketNotificationService::notifyTicketClosed($ticket);
```

---

### 7. **Tự động đóng ticket sau 3 ngày** ⭐ MỚI CẬP NHẬT
**Trigger**: Cron job chạy hàng ngày lúc 01:00

**Điều kiện**:
- Trạng thái: `responded`
- `last_staff_response_at` > 3 ngày

**Thông báo**:
- ✅ Gửi cho: Customer
- ✅ Tiêu đề: "Ticket #X đã tự động đóng"
- ✅ Nội dung: "Ticket đã được tự động đóng do không có phản hồi sau 3 ngày"

**Email**:
- ✅ Gửi cho: Customer
- ✅ Template: `ticket_auto_closed` (fallback: `ticket_closed`)
- ✅ Nội dung: Lý do đóng + link ticket

**File**: `app\Console\Kernel.php`
```php
$schedule->call(function () {
    $tickets = Ticket::where('status', Ticket::STATUS_RESPONDED)
        ->where('last_staff_response_at', '<=', now()->subDays(3))
        ->get();
    
    foreach ($tickets as $ticket) {
        $ticket->update(['status' => Ticket::STATUS_CLOSED]);
        TicketNotificationService::notifyTicketAutoClosed($ticket);
    }
})->dailyAt('01:00');
```

**Service**: `TicketNotificationService@notifyTicketAutoClosed`

---

## 📊 Bảng tổng hợp

| Sự kiện | Người nhận TB | Email | Trạng thái mới |
|---------|---------------|-------|----------------|
| Tạo ticket mới | Admin + Staff | Customer | `new` |
| Customer reply | Admin + Staff | - | `in_progress` |
| Staff reply | Customer | Customer | `responded` |
| Admin assign | Customer + Staff | Cả 2 | Không đổi |
| Staff claim | Customer | - | Không đổi |
| Admin đóng | Customer | Customer | `closed` |
| **Auto-close 3 ngày** | **Customer** | **Customer** | **`closed`** |

---

## 🔔 Chi tiết Notification Service

### Methods có sẵn:
1. ✅ `notifyNewTicket($ticket)` - Ticket mới
2. ✅ `notifyCustomerReply($ticket)` - Customer phản hồi
3. ✅ `notifyStaffReply($ticket, $sender)` - Staff phản hồi
4. ✅ `notifyTicketAssigned($ticket, $staff)` - Gán ticket
5. ✅ `notifyTicketClaimed($ticket, $staff)` - Claim ticket
6. ✅ `notifyTicketClosed($ticket)` - Đóng thủ công
7. ✅ `notifyTicketCompleted($ticket)` - Hoàn thành (deprecated)
8. ⭐ **`notifyTicketAutoClosed($ticket)`** - Tự động đóng (MỚI)

---

## 📧 Email Templates cần có

### Đã có:
1. ✅ `ticket_created` - Ticket được tạo
2. ✅ `ticket_replied` - Có phản hồi mới
3. ✅ `ticket_assigned` - Ticket được gán (customer)
4. ✅ `ticket_assigned_staff` - Ticket được gán (staff)
5. ✅ `ticket_closed` - Ticket đóng

### Nên thêm:
6. ⭐ `ticket_auto_closed` - Ticket tự động đóng (fallback: `ticket_closed`)

**Nội dung template `ticket_auto_closed`**:
```
Xin chào {{user_name}},

Ticket #{{ticket_id}} - "{{ticket_subject}}" của bạn đã được tự động đóng.

Lý do: Không có phản hồi từ bạn sau 3 ngày kể từ khi nhân viên phản hồi lần cuối.

Nếu bạn vẫn cần hỗ trợ, vui lòng tạo ticket mới hoặc liên hệ với chúng tôi.

Xem chi tiết: {{ticket_link}}

Trân trọng,
{{app_name}}
```

---

## ✅ Checklist hoàn thành

- [x] Ticket mới → Thông báo + Email
- [x] Customer reply → Thông báo
- [x] Staff reply → Thông báo + Email + Chuyển trạng thái `responded`
- [x] Admin assign → Thông báo + Email
- [x] Staff claim → Thông báo
- [x] Admin đóng → Thông báo + Email
- [x] **Auto-close 3 ngày → Thông báo + Email** ⭐ MỚI

---

## 🔄 Luồng trạng thái hoàn chỉnh

```
new (Mới tạo)
  ↓ Customer/Staff reply
in_progress (Đang xử lý)
  ↓ Staff reply
responded (Đã phản hồi)
  ↓ Customer reply
in_progress
  ↓ Staff reply
responded
  ↓ Sau 3 ngày không reply
closed (Đóng) ← Gửi thông báo + email
```

---

## 🚀 Cách test

### Test auto-close:
```php
// Tạo ticket test
$ticket = Ticket::create([...]);
$ticket->update([
    'status' => 'responded',
    'last_staff_response_at' => now()->subDays(4) // 4 ngày trước
]);

// Chạy cron job thủ công
php artisan schedule:run

// Hoặc test trực tiếp
$tickets = Ticket::where('status', 'responded')
    ->where('last_staff_response_at', '<=', now()->subDays(3))
    ->get();

foreach ($tickets as $ticket) {
    $ticket->update(['status' => 'closed']);
    TicketNotificationService::notifyTicketAutoClosed($ticket);
}
```

### Kiểm tra:
1. ✅ Ticket chuyển sang `closed`
2. ✅ Notification được tạo trong bảng `notifications`
3. ✅ UserNotification được tạo cho customer
4. ✅ Email được queue (kiểm tra `jobs` table)
5. ✅ Customer nhận được email

---

## 📝 Notes

- ✅ Tất cả thông báo đều lưu vào database
- ✅ Email được queue (không block request)
- ✅ Có fallback template nếu template chính không tồn tại
- ✅ Log error nếu gửi email thất bại
- ✅ Realtime notification counter cập nhật tự động
- ✅ Toast notification hiện khi có thông báo mới

---

## 🎯 Kết luận

Hệ thống tickets đã **HOÀN CHỈNH** với:
- ✅ Thông báo đầy đủ cho tất cả trạng thái
- ✅ Email gửi đúng người đúng lúc
- ✅ Tự động đóng ticket có thông báo
- ✅ Realtime updates
- ✅ Error handling tốt
