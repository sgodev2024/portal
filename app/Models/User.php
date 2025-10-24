<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
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
     * Lấy tên vai trò (role name).
     */
    public function getRoleNameAttribute()
    {
        return match ($this->role) {
            1 => 'Admin',
            2 => 'Nhân viên',
            3 => 'Người dùng',
            default => 'Không xác định',
        };
    }

    /**
     * Kiểm tra tài khoản có phải admin không.
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
    /**
     * Kiểm tra tài khoản có đang hoạt động không.
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }


protected static function boot()
{
    parent::boot();
    
   
    static::created(function ($user) {
        if ($user->role == 3) { 
            UserFolder::create([
                'user_id' => $user->id,
                'parent_id' => null,
                'name' => 'user_' . $user->id,
                'path' => 'users/user_' . $user->id,
                'description' => 'Thư mục gốc',
                'is_root' => true,
            ]);
            
            
           UserStorageQuota::create([
                'user_id' => $user->id,
                'quota_limit' => 1073741824, 
                'used_space' => 0,
            ]);
            

            Storage::disk('public')->makeDirectory('users/user_' . $user->id);
        }
    });
    
    static::deleting(function ($user) {
        if ($user->role == 3) {
            
            $userFiles = UserFile::where('user_id', $user->id)->get();
            foreach ($userFiles as $file) {
                if (Storage::disk('public')->exists($file->file_path)) {
                    Storage::disk('public')->delete($file->file_path);
                }
            }
            
            // Xóa physical folder
            Storage::disk('public')->deleteDirectory('users/user_' . $user->id);
            
            // Xóa records
            UserFile::where('user_id', $user->id)->delete();
            UserFolder::where('user_id', $user->id)->delete();
            UserStorageQuota::where('user_id', $user->id)->delete();
            UserFileActivity::where('user_id', $user->id)->delete();
        }
    });
}
}
