<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stmt extends Model
{
    use HasFactory;

    protected $table = 'stmt';

    protected $fillable = [
        'mail_username',
        'mail_password',
        'mail_from_name',
        'notification_emails',
    ];

    protected $casts = [
        'notification_emails' => 'array',
    ];
}
