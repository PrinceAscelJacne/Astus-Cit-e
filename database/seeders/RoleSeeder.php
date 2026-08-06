<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Support\Droits;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Les trois rôles livrés avec l'application, et les droits qu'ils portent
     * par défaut. L'administrateur peut ensuite créer ses propres rôles et
     * poser des dérogations sur une personne précise.
     *
     * Le Boss n'a pas de droits listés : il les a tous par construction.
     */
    public function run(): void
    {
        $defauts = [
            Role::EMPLOYE => [
                'description' => 'Travaille sur les projets auxquels il est affecté.',
                'droits' => [],
            ],
            Role::CHEF_DEPARTEMENT => [
                'description' => 'Pilote les projets de son département et son équipe.',
                'droits' => [
                    Droits::PROJETS_CREER,
                ],
            ],
            Role::BOSS => [
                'description' => 'Accès complet à l\'application.',
                'droits' => Droits::toutes(),
            ],
        ];

        foreach (Role::LIBELLES as $id => $libelle) {
            $role = Role::updateOrCreate(
                ['id' => $id],
                [
                    'name' => $libelle,
                    'description' => $defauts[$id]['description'],
                    'systeme' => true,
                ]
            );

            // Ne pas écraser une configuration déjà ajustée par
            // l'administrateur : on ne pose les droits qu'à la création.
            if ($role->droits()->count() === 0 && ! empty($defauts[$id]['droits'])) {
                $role->definirDroits($defauts[$id]['droits']);
            }
        }
    }
}
