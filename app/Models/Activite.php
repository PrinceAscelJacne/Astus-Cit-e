<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activite extends Model
{
    /**
     * Libellés lisibles des actions consignées.
     */
    public const LIBELLES = [
        'cree' => 'a créé',
        'modifie' => 'a modifié',
        'supprime' => 'a supprimé',
        'archive' => 'a archivé',
        'restaure' => 'a restauré',
        'publie' => 'a publié',
        'refuse' => 'a refusé',
        'ajoute_equipe' => 'a ajouté au projet',
        'retire_equipe' => 'a retiré du projet',
    ];

    protected $fillable = [
        'user_id', 'auteur_nom', 'action', 'sujet_type', 'sujet_id',
        'sujet_libelle', 'project_id', 'details',
    ];

    protected $casts = ['details' => 'array'];

    public function auteur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Nom du type touché, en clair : « Projet », « Fichier »…
     */
    public function typeLisible(): string
    {
        return match (class_basename($this->sujet_type)) {
            'Project' => 'le projet',
            'File' => 'le fichier',
            'User' => 'l\'utilisateur',
            'Temoignage' => 'le témoignage',
            'Department' => 'le département',
            'Role' => 'le rôle',
            default => 'l\'élément',
        };
    }

    public function actionLisible(): string
    {
        return self::LIBELLES[$this->action] ?? $this->action;
    }

    /**
     * Phrase complète : « Marie Adjovi a modifié le fichier contrat.docx ».
     */
    public function resume(): string
    {
        return trim(sprintf(
            '%s %s %s %s',
            $this->auteur_nom ?: 'Quelqu\'un',
            $this->actionLisible(),
            $this->typeLisible(),
            $this->sujet_libelle ?: ''
        ));
    }
}
