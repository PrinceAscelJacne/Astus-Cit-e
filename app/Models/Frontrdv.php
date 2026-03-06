<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Frontrdv extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'email', 'phone', 'date', 'message'];
}
