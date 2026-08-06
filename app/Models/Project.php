<?php

namespace App\Models;

use App\Support\ConsigneLesActivites;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory, ConsigneLesActivites;

    /**
     * États d'avancement du travail. Distincts de « status », qui sert au
     * classement actif / archivé.
     */
    public const AVANCEMENTS = [
        'a_faire' => 'À faire',
        'en_cours' => 'En cours',
        'en_relecture' => 'En relecture',
        'termine' => 'Terminé',
    ];

    protected $fillable = [
        'name', 'description', 'filename', 'path', 'department_id',
        'user_id', 'status', 'avancement', 'echeance',
    ];

    protected $casts = ['echeance' => 'date'];

    /** Attribut servant de libellé dans le journal d'activité. */
    protected $champLibelle = 'name';

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    /**
     * Créateur du projet. Conservé comme trace d'origine ; l'équipe de travail
     * passe par la relation membres().
     */
    public function createur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Équipe du projet : plusieurs personnes peuvent y travailler.
     */
    public function membres()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role_projet', 'ajoute_par')
            ->withTimestamps();
    }

    public function responsables()
    {
        return $this->membres()->wherePivot('role_projet', 'responsable');
    }

    /**
     * Fil chronologique de tout ce qui a été fait sur ce projet et ses
     * fichiers.
     */
    public function journal()
    {
        return $this->hasMany(Activite::class)->latest();
    }

    public function estMembre(User $utilisateur): bool
    {
        return $this->membres->contains('id', $utilisateur->id)
            || (int) $this->user_id === (int) $utilisateur->id;
    }

    public function avancementLisible(): string
    {
        return self::AVANCEMENTS[$this->avancement] ?? 'À faire';
    }

    /**
     * Part d'avancement, pour la barre de progression.
     */
    public function avancementPourcent(): int
    {
        return match ($this->avancement) {
            'en_cours' => 33,
            'en_relecture' => 66,
            'termine' => 100,
            default => 0,
        };
    }

    public function estEnRetard(): bool
    {
        return $this->echeance
            && $this->avancement !== 'termine'
            && $this->echeance->isPast();
    }
}
