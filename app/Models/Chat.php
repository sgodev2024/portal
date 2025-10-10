<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\ChatLog;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'staff_id',
        'status',
        'content',
        'last_message_at',
    ];

    protected $casts = [
        'content' => 'array',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function logs()
    {
        return $this->hasMany(ChatLog::class, 'chat_id');
    }
}
