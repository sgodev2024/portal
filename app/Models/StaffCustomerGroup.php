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
        'is_primary'
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
    

    public function customerGroup()
    {
        return $this->belongsTo(CustomerGroup::class, 'customer_group_id');
    }
}