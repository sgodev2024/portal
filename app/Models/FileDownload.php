<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileDownload extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'file_id',
        'user_id', 
        'downloaded_at',
        'ip_address'
    ];

    protected $casts = [
        'downloaded_at' => 'datetime',
    ];

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

