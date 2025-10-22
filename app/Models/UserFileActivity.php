<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class UserFileActivity extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id', 'file_id', 'folder_id', 'action', 
        'description', 'ip_address', 'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function file()
    {
        return $this->belongsTo(UserFile::class, 'file_id');
    }

    public function folder()
    {
        return $this->belongsTo(UserFolder::class, 'folder_id');
    }
}

