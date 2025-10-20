<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_active',
    ];

    /**
     * Quan hệ: thuộc tính có nhiều user
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'customer_attribute_user', 'customer_attribute_id', 'user_id');
    }
}
