<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    /**
     * La colonne s'appelle « filename » : « name » n'existe pas sur cette table.
     */
    protected $fillable = [
        'filename', 'path', 'type', 'status', 'project_id', 'user_id',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
