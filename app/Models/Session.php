<?php

namespace App\Models;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasApiTokens, Notifiable;
    protected $table = 'sessions';

    protected $fillable = [
        'id',
        'user_id',
        'ip_address',
        'user_agent',
        'payload',
        'last_activity',
    ];

    protected $primaryKey = 'id';
    public $incrementing = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function isMobile()
    {
        $userAgent = $this->user_agent;

        $mobileAgents = ['Mobile', 'Android', 'Silk/', 'Kindle', 'BlackBerry', 'Opera Mini', 'Opera Mobi'];

        foreach ($mobileAgents as $agent) {
            if (stripos($userAgent, $agent) !== false) {
                return true;
            }
        }

        return false;
    }
}

