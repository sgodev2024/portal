<?php

namespace App\Models;

use App\Models\UserFile;
use App\Models\UserFolder;
use App\Models\UserStorageQuota;
use App\Models\UserFileActivity;
use App\Models\CustomerGroup;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'id',
        'account_id',
        'name',
        'email',
        'company',
        'department',
        'position',
        'address',
        'password',
        'avatar',
        'role',
        'is_active',
        'must_update_profile',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Quan hệ: user có thể thuộc nhiều nhóm khách hàng
     */
    public function groups()
    {
        return $this->belongsToMany(CustomerGroup::class, 'customer_group_user', 'user_id', 'customer_group_id');
    }

    /**
     * Quan hệ: nhân viên phụ trách các nhóm khách hàng
     */
    public function managedGroups()
    {
        return $this->belongsToMany(CustomerGroup::class, 'staff_customer_group', 'staff_id', 'customer_group_id')
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }

    /**
     * Quan hệ: nhân viên phụ trách nhóm (chỉ nhân viên)
     */
    public function staffManagedGroups()
    {
        return $this->hasMany(StaffCustomerGroup::class, 'staff_id');
    }

    /**
     * Lấy tên vai trò (role name)
     */
    public function getRoleNameAttribute()
    {
        $locale = app()->getLocale();
        
        if ($locale == 'de') {
            return match ($this->role) {
                1 => 'Administrator',
                2 => 'Mitarbeiter',
                3 => 'Kunde',
                default => 'Unbekannt',
            };
        }
        
        // Default Vietnamese
        return match ($this->role) {
            1 => 'Quản trị viên',
            2 => 'Nhân viên',
            3 => 'Khách hàng',
            default => 'Không xác định',
        };
    }

    /**
     * Kiểm tra vai trò
     */
    public function isAdmin()
    {
        return (int)$this->role === 1;
    }

    public function isStaff()
    {
        return (int)$this->role === 2;
    }

    public function isUser()
    {
        return (int)$this->role === 3;
    }

    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /**
     * ==========================================
     * BOOT METHOD - TẠO FOLDER & QUOTA TỰ ĐỘNG
     * ==========================================
     */
    protected static function boot()
    {
        parent::boot();

        // ✅ KHI TẠO USER (role = 3 là customer)
        static::created(function ($user) {
            if ($user->role == 3) {
                DB::beginTransaction();
                try {
                    // 1️⃣ TẠO ROOT FOLDER
                    UserFolder::create([
                        'user_id' => $user->id,
                        'parent_id' => null,
                        'name' => 'user_' . $user->id,
                        'path' => '/',
                        'description' => 'Thư mục gốc',
                        'is_root' => true,
                    ]);

                    // 2️⃣ TẠO STORAGE QUOTA (1GB = 1073741824 bytes)
                    UserStorageQuota::create([
                        'user_id' => $user->id,
                        'quota_limit' => 1073741824,  // 1GB
                        'used_space' => 0,
                    ]);

                    // 3️⃣ TẠO THỰC THƯ MỤC TRÊN DISK
                    Storage::disk('public')->makeDirectory("user_files/{$user->id}");

                    DB::commit();

                } catch (\Exception $e) {
                    DB::rollBack();
                    \Illuminate\Support\Facades\Log::error('Lỗi tạo folder: ' . $e->getMessage());
                    throw $e;
                }
            }
        });

        // ✅ KHI XÓA USER
        static::deleting(function ($user) {
            if ($user->role == 3) {
                DB::beginTransaction();
                try {
                    // 1️⃣ Lấy tất cả files của user
                    $userFiles = UserFile::where('user_id', $user->id)->get();

                    // 2️⃣ Xóa file vật lý
                    foreach ($userFiles as $file) {
                        if (Storage::disk('public')->exists($file->file_path)) {
                            Storage::disk('public')->delete($file->file_path);
                        }
                    }

                    // 3️⃣ Xóa thư mục vật lý trên disk
                    if (Storage::disk('public')->exists("user_files/{$user->id}")) {
                        Storage::disk('public')->deleteDirectory("user_files/{$user->id}");
                    }

                    // 4️⃣ Xóa database records
                    UserFile::where('user_id', $user->id)->delete();
                    UserFolder::where('user_id', $user->id)->delete();
                    UserStorageQuota::where('user_id', $user->id)->delete();
                    UserFileActivity::where('user_id', $user->id)->delete();

                    DB::commit();

                } catch (\Exception $e) {
                    DB::rollBack();
                    \Illuminate\Support\Facades\Log::error('Lỗi xóa user: ' . $e->getMessage());
                    throw $e;
                }
            }
        });
    }
}

?>