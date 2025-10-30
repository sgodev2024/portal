<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'location',
        'total_units',
        'status',
    ];

    /**
     * Get the users (customers) for the project group (many-to-many).
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_project_group', 'project_group_id', 'user_id')
                    ->withTimestamps();
    }
}
