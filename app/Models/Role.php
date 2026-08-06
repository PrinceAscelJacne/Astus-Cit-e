<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * Identifiants des rôles, tels qu'utilisés par les vues et les seeders.
     */
    public const EMPLOYE = 1;
    public const CHEF_DEPARTEMENT = 2;
    public const BOSS = 3;

    /**
     * Libellés affichables, indexés par identifiant.
     *
     * @var array<int, string>
     */
    public const LIBELLES = [
        self::EMPLOYE => 'Employé',
        self::CHEF_DEPARTEMENT => 'Chef département',
        self::BOSS => 'Boss',
    ];

    protected $fillable = ['name'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
