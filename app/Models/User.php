<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
     * Quan hệ: user có thể có nhiều thuộc tính khách hàng
     */
    public function attributes()
    {
        return $this->belongsToMany(CustomerAttribute::class, 'customer_attribute_user', 'user_id', 'customer_attribute_id');
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
}
