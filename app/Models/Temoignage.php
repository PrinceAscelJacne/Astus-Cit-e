<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Temoignage extends Model
{
    use HasFactory;

    public const EN_ATTENTE = 'en_attente';
    public const PUBLIE = 'publie';
    public const REFUSE = 'refuse';

    /**
     * Services proposés, tels qu'ils apparaissent sur le site vitrine.
     * Sert aussi de liste de validation du formulaire public.
     *
     * @var array<int, string>
     */
    public const SERVICES = [
        'Affiches et Flyers',
        'Design Graphique',
        'Livres et Brochures',
        'Supports Marketing',
        'Illustrations Personnalisées',
        'Identité Visuelle',
    ];

    /**
     * « statut » est volontairement absent : il ne doit jamais être fixé
     * depuis le formulaire public, sans quoi n'importe qui publierait
     * directement sur le site.
     *
     * @var array<int, string>
     */
    protected $fillable = ['nom', 'entreprise', 'service', 'note', 'message'];

    protected $casts = ['note' => 'integer'];

    public function scopePublies(Builder $query): Builder
    {
        return $query->where('statut', self::PUBLIE);
    }

    public function scopeEnAttente(Builder $query): Builder
    {
        return $query->where('statut', self::EN_ATTENTE);
    }

    /**
     * Initiales servant de pastille lorsqu'aucune photo n'est disponible.
     */
    public function initiales(): string
    {
        preg_match_all('/\b\p{L}/u', $this->nom, $lettres);

        return mb_strtoupper(implode('', array_slice($lettres[0], 0, 2)));
    }
}
