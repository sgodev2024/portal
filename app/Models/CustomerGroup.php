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
}
