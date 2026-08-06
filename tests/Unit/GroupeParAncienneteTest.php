<?php

namespace Tests\Unit;

use App\Support\GroupeParAnciennete;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

/**
 * Carbon 3, livré avec Laravel 12, renvoie un flottant pour diffInDays là où
 * Carbon 2 renvoyait un entier tronqué. Un élément vieux de « 7 jours et
 * 2 heures » basculait donc de catégorie après la migration, sans qu'aucun
 * test ne le signale. Ces cas verrouillent les bornes.
 */
class GroupeParAncienneteTest extends TestCase
{
    use GroupeParAnciennete;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function categorieePour(CarbonInterface $created): string
    {
        $items = new Collection([(object) ['created_at' => $created]]);

        return (string) $this->grouperParDate($items)->keys()->first();
    }

    public function test_les_bornes_de_regroupement_sont_stables(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-06 14:00:00'));
        $maintenant = Carbon::getTestNow();

        $cas = [
            "Aujourd'hui" => $maintenant->copy()->subHours(3),
            'Hier' => $maintenant->copy()->subDay(),
            'Cette semaine' => $maintenant->copy()->subDays(5),
            'Ce mois-ci' => $maintenant->copy()->subDays(20),
            'Cette année' => $maintenant->copy()->subDays(200),
            "Il y a plus d'un an" => $maintenant->copy()->subDays(500),
        ];

        foreach ($cas as $attendu => $date) {
            $this->assertSame($attendu, $this->categorieePour($date));
        }
    }

    public function test_la_fraction_de_journee_ne_fait_pas_changer_de_categorie(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-06 14:00:00'));

        // Exactement 7 jours plus 2 heures : diffInDays vaut 7.08 sous Carbon 3.
        // Sans normalisation à minuit, ce cas sortait de « Cette semaine ».
        $limite = Carbon::getTestNow()->copy()->subDays(7)->subHours(2);

        $this->assertSame('Cette semaine', $this->categorieePour($limite));
    }
}
