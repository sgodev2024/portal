<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'staff_id',
        'status',
        'last_message_at',
    ];

    // 🔗 Quan hệ
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function logs()
    {
        return $this->hasMany(ChatLog::class);
    }
}
