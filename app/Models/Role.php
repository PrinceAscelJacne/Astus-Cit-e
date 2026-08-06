<?php

namespace App\Models;

use App\Support\ConsigneLesActivites;
use App\Support\Droits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory, ConsigneLesActivites;

    /**
     * Identifiants des trois rôles livrés avec l'application. Ils servent de
     * réglage par défaut et ne sont pas supprimables ; l'administrateur peut
     * en créer d'autres.
     */
    public const EMPLOYE = 1;
    public const CHEF_DEPARTEMENT = 2;
    public const BOSS = 3;

    /**
     * @var array<int, string>
     */
    public const LIBELLES = [
        self::EMPLOYE => 'Employé',
        self::CHEF_DEPARTEMENT => 'Chef département',
        self::BOSS => 'Boss',
    ];

    protected $fillable = ['name', 'description', 'systeme'];

    protected $casts = ['systeme' => 'boolean'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function droits()
    {
        return $this->hasMany(RolePermission::class);
    }

    /**
     * Le rôle porte-t-il ce droit ?
     */
    public function porte(string $droit): bool
    {
        return $this->droits->contains('droit', $droit);
    }

    /**
     * Remplace le jeu de droits du rôle.
     *
     * @param  array<int, string>  $droits
     */
    public function definirDroits(array $droits): void
    {
        $valides = array_values(array_filter($droits, [Droits::class, 'existe']));

        $this->droits()->delete();

        foreach ($valides as $droit) {
            $this->droits()->create(['droit' => $droit]);
        }

        $this->load('droits');
    }

    /**
     * Un rôle système ne se supprime pas : le code s'appuie dessus.
     */
    public function estSupprimable(): bool
    {
        return ! $this->systeme && $this->users()->count() === 0;
    }
}
