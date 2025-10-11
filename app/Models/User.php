<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Các cột được phép gán hàng loạt (mass assignable).
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'gender',
        'birthday',
        'tax_code',
        'password',
        'identity_number',
        'must_update_profile',
        'role',
        'is_active',
    ];

    /**
     * Các cột sẽ bị ẩn khi chuyển sang JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Các cột cần ép kiểu.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'must_update_profile' => 'boolean',
    ];

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
}
