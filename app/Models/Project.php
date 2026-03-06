<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'filename', 'path', 'department_id', 'user_id', 'status', ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function files()
    {
        return $this->hasMany(File::class);
    }
    /*public function departments()
    {
        return $this->belongsToMany(File::class);
    }*/
}
