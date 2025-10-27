<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    /**
     * Quan hệ: nhóm có nhiều khách hàng (users)
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'customer_group_user', 'customer_group_id', 'user_id');
    }

    /**
     * Quan hệ: nhóm được phụ trách bởi nhiều nhân viên
     */
    public function staff()
    {
        return $this->belongsToMany(User::class, 'staff_customer_group', 'customer_group_id', 'staff_id')
                    ->withTimestamps();
    }

    /**
     * Quan hệ: nhân viên phụ trách nhóm
     */
    public function staffManagedGroups()
    {
        return $this->hasMany(StaffCustomerGroup::class, 'customer_group_id');
    }

    /**
     * Lấy nhân viên chính phụ trách nhóm
     */
    public function primaryStaff()
    {
        return $this->staff()->wherePivot('is_primary', true)->first();
    }

    /**
     * Scope: chỉ lấy nhóm active
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
