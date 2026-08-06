<?php

namespace App\Support;

/**
 * Catalogue des droits de l'application.
 *
 * Ce sont des fonctionnalités : elles ne se créent pas depuis l'interface,
 * elles existent parce que le code les implémente. L'administrateur, lui,
 * compose des rôles à partir de cette liste et peut poser des dérogations
 * sur une personne précise.
 */
final class Droits
{
    public const PROJETS_CREER = 'projets.creer';
    public const PROJETS_GERER_TOUS = 'projets.gerer_tous';
    public const FICHIERS_GERER_TOUS = 'fichiers.gerer_tous';
    public const MESSAGES = 'messages';
    public const RENDEZVOUS = 'rendezvous';
    public const TEMOIGNAGES = 'temoignages';
    public const UTILISATEURS = 'utilisateurs';
    public const ROLES = 'roles';
    public const DEPARTEMENTS = 'departements';
    public const JOURNAL = 'journal';

    /**
     * Regroupement pour l'écran de configuration : libellé et explication.
     *
     * @return array<string, array<string, array{libelle: string, aide: string}>>
     */
    public static function catalogue(): array
    {
        return [
            'Projets et fichiers' => [
                self::PROJETS_CREER => [
                    'libelle' => 'Créer des projets',
                    'aide' => 'Ouvrir un nouveau projet et constituer son équipe.',
                ],
                self::PROJETS_GERER_TOUS => [
                    'libelle' => 'Gérer tous les projets',
                    'aide' => 'Modifier et archiver les projets même sans en faire partie.',
                ],
                self::FICHIERS_GERER_TOUS => [
                    'libelle' => 'Gérer tous les fichiers',
                    'aide' => 'Consulter, modifier et supprimer les fichiers de tout le monde.',
                ],
            ],
            'Site vitrine' => [
                self::MESSAGES => [
                    'libelle' => 'Messages de contact',
                    'aide' => 'Lire les messages reçus depuis le site et y répondre.',
                ],
                self::RENDEZVOUS => [
                    'libelle' => 'Demandes de rendez-vous',
                    'aide' => 'Traiter les demandes de rendez-vous et les marquer comme honorées.',
                ],
                self::TEMOIGNAGES => [
                    'libelle' => 'Témoignages clients',
                    'aide' => 'Publier, retirer ou supprimer les témoignages déposés.',
                ],
            ],
            'Administration' => [
                self::UTILISATEURS => [
                    'libelle' => 'Utilisateurs',
                    'aide' => 'Créer des comptes et régler les droits de chacun.',
                ],
                self::ROLES => [
                    'libelle' => 'Rôles',
                    'aide' => 'Créer des rôles et choisir ce qu\'ils permettent.',
                ],
                self::DEPARTEMENTS => [
                    'libelle' => 'Départements',
                    'aide' => 'Créer et supprimer les départements.',
                ],
                self::JOURNAL => [
                    'libelle' => 'Journal d\'activité',
                    'aide' => 'Consulter l\'historique de toutes les actions.',
                ],
            ],
        ];
    }

    /**
     * Toutes les clés, à plat.
     *
     * @return array<int, string>
     */
    public static function toutes(): array
    {
        $cles = [];

        foreach (self::catalogue() as $droits) {
            $cles = array_merge($cles, array_keys($droits));
        }

        return $cles;
    }

    public static function libelle(string $droit): string
    {
        foreach (self::catalogue() as $groupe) {
            if (isset($groupe[$droit])) {
                return $groupe[$droit]['libelle'];
            }
        }

        return $droit;
    }

    public static function existe(string $droit): bool
    {
        return in_array($droit, self::toutes(), true);
    }
}
