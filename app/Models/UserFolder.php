<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFolder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parent_id',
        'name',
        'path',
        'description',
        'is_root'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(UserFolder::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(UserFolder::class, 'parent_id');
    }

    public function files()
    {
        return $this->hasMany(UserFile::class, 'folder_id');
    }

    // Lấy breadcrumb path
    public function getBreadcrumbAttribute()
    {
        $breadcrumb = collect([$this]);
        $parent = $this->parent;

        while ($parent) {
            $breadcrumb->prepend($parent);
            $parent = $parent->parent;
        }

        return $breadcrumb;
    }
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($folder) {
            if ($folder->is_root) {
                throw new \Exception('Không thể xóa thư mục gốc!');
            }
        });
        static::updating(function ($folder) {
            if ($folder->is_root && $folder->isDirty(['name', 'parent_id'])) {
                throw new \Exception('Không thể thay đổi thư mục gốc!');
            }
        });
    }

    public function isRoot()
    {
        return $this->is_root === true;
    }
}
