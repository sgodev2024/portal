<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStorageQuota extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'quota_limit', 'used_space', 'last_calculated_at'
    ];

    protected $casts = [
        'quota_limit' => 'integer',
        'used_space' => 'integer',
        'last_calculated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getUsedPercentageAttribute()
    {
        if ($this->quota_limit == 0) return 0;
        return round(($this->used_space / $this->quota_limit) * 100, 2);
    }

    public function getRemainingSpaceAttribute()
    {
        return max(0, $this->quota_limit - $this->used_space);
    }

    public function getUsedSpaceFormattedAttribute()
    {
        return $this->formatBytes($this->used_space);
    }

    public function getQuotaLimitFormattedAttribute()
    {
        return $this->formatBytes($this->quota_limit);
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
}