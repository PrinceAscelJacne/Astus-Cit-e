<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Renseigne les libellés des rôles. Les trois lignes existaient déjà en
     * base mais avec un nom vide, ce qui laissait la colonne « Rôle » de la
     * table des utilisateurs blanche.
     */
    public function run(): void
    {
        foreach (Role::LIBELLES as $id => $libelle) {
            Role::updateOrCreate(['id' => $id], ['name' => $libelle]);
        }
    }
}
