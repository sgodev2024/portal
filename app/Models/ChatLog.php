<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Chat;
use App\Models\User;

class ChatLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'changed_by',
        'action',
        'note',
    ];

    public function chat()
    {
        return $this->belongsTo(Chat::class, 'chat_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
