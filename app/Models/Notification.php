<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'title',
        'content',
        'attachment_path',
        'target_role',
        'created_by',
        'is_sent',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'is_sent' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function userNotifications()
    {
        return $this->hasMany(UserNotification::class);
    }
}
