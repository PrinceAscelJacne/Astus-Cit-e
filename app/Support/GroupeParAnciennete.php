<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Regroupe une collection d'enregistrements par ancienneté de création.
 *
 * La comparaison se fait sur des journées entières. Carbon 3 (livré avec
 * Laravel 12) renvoie un flottant pour diffInDays : un élément créé il y a
 * « 7 jours et 2 heures » donnait 7 sous Carbon 2 mais 7.08 désormais, ce qui
 * le faisait basculer d'une catégorie à l'autre. Ramener les deux dates à
 * minuit rend le classement stable, quelle que soit la version de Carbon.
 */
trait GroupeParAnciennete
{
    protected function grouperParDate(Collection $items): Collection
    {
        $aujourdhui = Carbon::now()->startOfDay();

        return $items->groupBy(function ($item) use ($aujourdhui) {
            $created = Carbon::parse($item->created_at);
            $jours = (int) $created->copy()->startOfDay()->diffInDays($aujourdhui);

            if ($created->isToday()) {
                return "Aujourd'hui";
            }

            if ($created->isYesterday()) {
                return 'Hier';
            }

            return match (true) {
                $jours <= 7 => 'Cette semaine',
                $jours <= 30 => 'Ce mois-ci',
                $jours <= 365 => 'Cette année',
                default => "Il y a plus d'un an",
            };
        });
    }
}
