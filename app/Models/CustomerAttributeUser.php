<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAttributeUser extends Model
{
    use HasFactory;

    protected $table = 'customer_attribute_user';

    protected $fillable = [
        'user_id',
        'customer_attribute_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function attribute()
    {
        return $this->belongsTo(CustomerAttribute::class, 'customer_attribute_id');
    }
}
