<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class StaffImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use Importable;

    private $errors = [];
    private $failures = [];
    private $successCount = 0;
    private $rowNumber = 0;

    public function model(array $row)
    {
        $this->rowNumber++;
        if (empty($row['name']) || empty($row['phone']) || empty($row['email'])) {
            Log::warning("Dòng {$this->rowNumber} thiếu dữ liệu bắt buộc");
            return null;
        }

        try {
            $accountId = $this->generateAccountId($row['phone']);

            $rawPassword = $row['password'] ?? '123456';

            $user = new User([
                'name'       => trim($row['name']),
                'account_id' => $accountId,
                'phone'      => trim($row['phone']),
                'email'      => trim($row['email']),
                'department' => trim($row['department'] ?? ''),
                'position'   => trim($row['position'] ?? ''),
                'password'   => Hash::make($rawPassword),
                'role'       => 2,
                'is_active'  => $this->parseIsActive($row['is_active'] ?? null),
            ]);

            $this->successCount++;
            Log::info("Dòng {$this->rowNumber} import thành công với account_id: {$accountId}");

            return $user;
        } catch (\Exception $e) {
            Log::error("Lỗi dòng {$this->rowNumber}: " . $e->getMessage());
            return null;
        }
    }

    public function rules(): array
    {
        return [
            '*.name'       => 'required|string|max:255',
            '*.phone'      => 'required|string|max:15|unique:users,phone',
            '*.email'      => 'required|email|unique:users,email',
            '*.department' => 'required|string|max:255',
            '*.position'   => 'required|string|max:255',
            '*.password'   => 'nullable|string|min:6',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.name.required'       => 'Họ tên không được để trống',
            '*.phone.required'      => 'Số điện thoại không được để trống',
            '*.phone.unique'        => 'Số điện thoại đã tồn tại',
            '*.email.required'      => 'Email không được để trống',
            '*.email.email'         => 'Email không hợp lệ',
            '*.email.unique'        => 'Email đã được sử dụng',
            '*.department.required' => 'Phòng ban không được để trống',
            '*.position.required'   => 'Chức vụ không được để trống',
            '*.password.min'        => 'Mật khẩu phải có ít nhất 6 ký tự',
        ];
    }

    public function onError(Throwable $e)
    {
        Log::error('Import error: ' . $e->getMessage());
        $this->errors[] = $e->getMessage();
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            Log::warning("Validation failed - Dòng {$failure->row()}: " . implode(', ', $failure->errors()));
        }
        $this->failures = array_merge($this->failures, $failures);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getFailures()
    {
        return $this->failures;
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }

    public function getRowNumber()
    {
        return $this->rowNumber;
    }

    /**
     * Parse giá trị is_active từ nhiều định dạng
     */
    private function parseIsActive($value)
    {
        if ($value === null) {
            return false;
        }

        $value = strtolower(trim($value));

        // Các giá trị được coi là true
        $trueValues = ['1', 'true', 'yes', 'có', 'active', 'kích hoạt'];

        return in_array($value, $trueValues);
    }

    /**
     * Sinh account_id từ số điện thoại
     * Bắt đầu từ 3 số cuối, nếu trùng thì tăng lên 4, 5... cho đến khi không trùng
     */
    private function generateAccountId($phone)
    {
        // Loại bỏ các ký tự không phải số
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (empty($phone)) {
            return 'ACC' . time();
        }

        // Bắt đầu từ 3 số cuối
        $length = 3;
        $maxLength = strlen($phone);

        while ($length <= $maxLength) {
            $accountId = substr($phone, -$length);

            // Kiểm tra xem account_id đã tồn tại chưa
            $exists = User::where('account_id', $accountId)->exists();

            if (!$exists) {
                return $accountId;
            }
            $length++;
        }
        return $phone . time();
    }
}
