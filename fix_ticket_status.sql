-- Script để sửa trạng thái tickets chưa gán
-- Chạy script này trong MySQL/phpMyAdmin

-- 1. Cập nhật enum để thêm 'responded'
ALTER TABLE tickets MODIFY COLUMN status ENUM('new', 'in_progress', 'responded', 'waiting_customer', 'completed', 'closed') DEFAULT 'new';

-- 2. Update tất cả tickets chưa có người phụ trách về status 'new'
UPDATE tickets 
SET status = 'new' 
WHERE assigned_staff_id IS NULL 
  AND status IN ('in_progress', 'waiting_customer', 'completed');

-- 3. Kiểm tra kết quả
SELECT id, subject, assigned_staff_id, status 
FROM tickets 
WHERE assigned_staff_id IS NULL 
ORDER BY created_at DESC;
