<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffCustomerGroup extends Model
{
    use HasFactory;

    protected $table = 'staff_customer_group';
    
 
    protected $fillable = [
        'staff_id',
        'customer_group_id',
    ];

    public $timestamps = true; 

    /**
     * Relationship: Staff
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
   
    /**
     * Relationship: Customer Group
     */
    public function customerGroup()
    {
        return $this->belongsTo(CustomerGroup::class, 'customer_group_id');
    }

    /**
     * ✅ Boot method: Đảm bảo 1 nhóm chỉ có 1 staff
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Kiểm tra trước khi tạo
            $existing = self::where('customer_group_id', $model->customer_group_id)->first();
            if ($existing) {
                throw new \Exception("Nhóm này đã có nhân viên {$existing->staff->name} phụ trách!");
            }
        });
    }
}