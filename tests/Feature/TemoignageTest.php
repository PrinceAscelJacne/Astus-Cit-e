<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Temoignage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TemoignageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (Role::LIBELLES as $id => $libelle) {
            Role::create(['id' => $id, 'name' => $libelle]);
        }
    }

    private function utilisateur(int $roleId): User
    {
        return User::factory()->create(['role_id' => $roleId]);
    }

    private function donneesValides(array $remplacements = []): array
    {
        return array_merge([
            'nom' => 'Marie Adjovi',
            'entreprise' => 'Boutique Élan',
            'service' => 'Design Graphique',
            'note' => 5,
            'message' => 'Travail impeccable et livraison dans les délais convenus.',
        ], $remplacements);
    }

    /* Dépôt public */

    public function test_un_visiteur_peut_deposer_un_temoignage(): void
    {
        $this->post('/frontend/temoignage', $this->donneesValides())
            ->assertRedirect();

        $temoignage = Temoignage::firstOrFail();

        $this->assertSame('Marie Adjovi', $temoignage->nom);
        $this->assertSame(Temoignage::EN_ATTENTE, $temoignage->statut);
    }

    public function test_le_statut_ne_peut_pas_etre_impose_depuis_le_formulaire(): void
    {
        // Sans protection, n'importe qui publierait directement sur le site.
        $this->post('/frontend/temoignage', $this->donneesValides(['statut' => Temoignage::PUBLIE]));

        $this->assertSame(Temoignage::EN_ATTENTE, Temoignage::firstOrFail()->statut);
    }

    public function test_un_service_hors_liste_est_refuse(): void
    {
        $this->post('/frontend/temoignage', $this->donneesValides(['service' => 'Service inventé']))
            ->assertSessionHasErrors('service');

        $this->assertSame(0, Temoignage::count());
    }

    public function test_un_message_trop_court_est_refuse(): void
    {
        $this->post('/frontend/temoignage', $this->donneesValides(['message' => 'Trop court']))
            ->assertSessionHasErrors('message');
    }

    public function test_une_note_hors_bornes_est_refusee(): void
    {
        $this->post('/frontend/temoignage', $this->donneesValides(['note' => 9]))
            ->assertSessionHasErrors('note');
    }

    /* Visibilité sur le site */

    public function test_un_temoignage_en_attente_n_apparait_pas_sur_le_site(): void
    {
        Temoignage::create($this->donneesValides(['nom' => 'Nom En Attente']));

        $this->get('/')->assertOk()->assertDontSee('Nom En Attente');
    }

    public function test_un_temoignage_publie_apparait_sur_le_site(): void
    {
        $temoignage = Temoignage::create($this->donneesValides(['nom' => 'Nom Publie']));
        $temoignage->forceFill(['statut' => Temoignage::PUBLIE])->save();

        $this->get('/')->assertOk()->assertSee('Nom Publie');
    }

    public function test_un_temoignage_refuse_n_apparait_pas_sur_le_site(): void
    {
        $temoignage = Temoignage::create($this->donneesValides(['nom' => 'Nom Refuse']));
        $temoignage->forceFill(['statut' => Temoignage::REFUSE])->save();

        $this->get('/')->assertOk()->assertDontSee('Nom Refuse');
    }

    /* Modération */

    public function test_la_moderation_est_inaccessible_sans_authentification(): void
    {
        $this->get('/dashboarde/temoignages')->assertRedirect('/login');
    }

    public function test_un_employe_ne_peut_pas_moderer(): void
    {
        $temoignage = Temoignage::create($this->donneesValides());

        $this->actingAs($this->utilisateur(Role::EMPLOYE))
            ->get('/dashboarde/temoignages')
            ->assertForbidden();

        $this->actingAs($this->utilisateur(Role::EMPLOYE))
            ->post("/dashboarde/temoignages/{$temoignage->id}/publier")
            ->assertForbidden();

        $this->assertSame(Temoignage::EN_ATTENTE, $temoignage->fresh()->statut);
    }

    public function test_le_boss_publie_un_temoignage(): void
    {
        $temoignage = Temoignage::create($this->donneesValides());

        $this->actingAs($this->utilisateur(Role::BOSS))
            ->post("/dashboarde/temoignages/{$temoignage->id}/publier")
            ->assertRedirect();

        $this->assertSame(Temoignage::PUBLIE, $temoignage->fresh()->statut);
    }

    public function test_le_boss_retire_un_temoignage_du_site(): void
    {
        $temoignage = Temoignage::create($this->donneesValides());
        $temoignage->forceFill(['statut' => Temoignage::PUBLIE])->save();

        $this->actingAs($this->utilisateur(Role::BOSS))
            ->post("/dashboarde/temoignages/{$temoignage->id}/refuser")
            ->assertRedirect();

        $this->assertSame(Temoignage::REFUSE, $temoignage->fresh()->statut);
    }

    public function test_le_boss_supprime_un_temoignage(): void
    {
        $temoignage = Temoignage::create($this->donneesValides());

        $this->actingAs($this->utilisateur(Role::BOSS))
            ->delete("/dashboarde/temoignages/{$temoignage->id}")
            ->assertRedirect();

        $this->assertSame(0, Temoignage::count());
    }

    public function test_les_initiales_servent_de_pastille(): void
    {
        $temoignage = Temoignage::create($this->donneesValides(['nom' => 'Marie Adjovi']));

        $this->assertSame('MA', $temoignage->initiales());
    }
}
