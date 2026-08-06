<?php

namespace App\Models;

use App\Support\ConsigneLesActivites;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Ce qui reste à faire sur un projet, et par qui.
 */
class Tache extends Model
{
    use ConsigneLesActivites;

    protected $table = 'taches';

    /** Attribut servant de libellé dans le journal d'activité. */
    protected $champLibelle = 'titre';

    public const STATUTS = [
        'a_faire' => 'À faire',
        'en_cours' => 'En cours',
        'fait' => 'Fait',
    ];

    protected $fillable = [
        'project_id', 'titre', 'description', 'statut',
        'assignee_id', 'cree_par', 'echeance', 'faite_le', 'faite_par',
    ];

    protected $casts = [
        'echeance' => 'date',
        'faite_le' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function scopeAFaire(Builder $query): Builder
    {
        return $query->where('statut', '!=', 'fait');
    }

    public function statutLisible(): string
    {
        return self::STATUTS[$this->statut] ?? 'À faire';
    }

    public function estEnRetard(): bool
    {
        return $this->echeance
            && $this->statut !== 'fait'
            && $this->echeance->isPast();
    }

    /**
     * Marque la tâche comme faite en retenant qui l'a terminée et quand :
     * sans cela, on sait ce qui est fait mais jamais par qui.
     */
    public function marquerFaite(User $utilisateur): void
    {
        $this->update([
            'statut' => 'fait',
            'faite_le' => now(),
            'faite_par' => $utilisateur->id,
        ]);
    }
}
