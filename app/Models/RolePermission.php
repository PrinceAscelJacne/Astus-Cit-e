<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Droit porté par un rôle.
 */
class RolePermission extends Model
{
    protected $table = 'role_permission';

    protected $fillable = ['role_id', 'droit'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
