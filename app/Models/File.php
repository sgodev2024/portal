<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'description', 'file_name', 'file_path',
        'file_type', 'file_size', 'uploaded_by',
        'file_category', // 'report' hoặc 'template'
        'recipients', // JSON array emails
        'sent_at', 'sent_by',
        'is_active', 'download_count'
    ];

    protected $casts = [
        'file_size' => 'integer',
        'recipients' => 'array',
        'sent_at' => 'datetime',
        'is_active' => 'boolean',
        'download_count' => 'integer',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function downloads()
    {
        return $this->hasMany(FileDownload::class);
    }

    public function getFileSizeFormattedAttribute()
    {
        return $this->formatBytes($this->file_size);
    }

    private function formatBytes($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }

    // Query scopes
    public function scopeReports($query)
    {
        return $query->where('file_category', 'report');
    }

    public function scopeTemplates($query)
    {
        return $query->where('file_category', 'template');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }
}
