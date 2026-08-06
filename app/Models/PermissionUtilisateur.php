<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Dérogation de droit posée sur une personne précise.
 *
 * Elle l'emporte sur ce que porte le rôle, dans les deux sens : accorder un
 * droit que le rôle n'a pas, ou le retirer alors que le rôle le donne.
 */
class PermissionUtilisateur extends Model
{
    protected $table = 'permission_user';

    protected $fillable = ['user_id', 'droit', 'accorde'];

    protected $casts = ['accorde' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
