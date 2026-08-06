<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Message échangé au sein d'un projet.
 *
 * L'équipe travaille à distance : ce fil garde la trace écrite des consignes
 * et des décisions, rattachée au projet plutôt que dispersée dans des
 * conversations privées.
 */
class Discussion extends Model
{
    protected $fillable = ['project_id', 'user_id', 'auteur_nom', 'corps', 'file_id'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function auteur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Le message peut porter sur un fichier précis plutôt que sur le projet
     * en général.
     */
    public function file()
    {
        return $this->belongsTo(File::class);
    }

    protected static function booted(): void
    {
        // Le nom est figé à l'écriture : le fil doit rester lisible même
        // après suppression du compte de son auteur.
        static::creating(function (Discussion $message) {
            if (! $message->auteur_nom && $message->user_id) {
                $auteur = User::find($message->user_id);
                $message->auteur_nom = $auteur
                    ? trim(($auteur->surname ?? '') . ' ' . $auteur->name)
                    : null;
            }
        });
    }
}
