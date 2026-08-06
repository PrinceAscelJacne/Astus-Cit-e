<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Temoignage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Vérifie la coquille du back-office : compilation des vues, structure, et
 * surtout le fait qu'un lien affiché mène toujours quelque part. Auparavant,
 * plusieurs entrées de menu apparaissaient pour tous les rôles et renvoyaient
 * une erreur 403 au clic.
 */
class CoquilleBackOfficeTest extends TestCase
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

    public function test_le_tableau_de_bord_s_affiche_pour_chaque_role(): void
    {
        foreach ([Role::EMPLOYE, Role::CHEF_DEPARTEMENT, Role::BOSS] as $role) {
            $this->actingAs($this->utilisateur($role))
                ->get('/dashboarde/dashboard')
                ->assertOk()
                ->assertSee('as-barre', false)
                ->assertSee('as-entete', false);
        }
    }

    public function test_la_barre_laterale_affiche_l_identite_de_l_utilisateur(): void
    {
        $boss = $this->utilisateur(Role::BOSS);

        $this->actingAs($boss)
            ->get('/dashboarde/dashboard')
            ->assertOk()
            ->assertSee($boss->name)
            ->assertSee('Boss');
    }

    public function test_un_employe_ne_voit_pas_les_entrees_reservees(): void
    {
        $reponse = $this->actingAs($this->utilisateur(Role::EMPLOYE))
            ->get('/dashboarde/dashboard')
            ->assertOk();

        // Ces pages sont réservées au Boss par les routes : les afficher
        // mènerait à une erreur 403.
        $reponse->assertDontSee('Table des projets')
                ->assertDontSee('Table des fichiers')
                ->assertDontSee('Départements')
                ->assertDontSee('Messages &amp; rendez-vous', false)
                ->assertDontSee('Inscrire un utilisateur');
    }

    public function test_le_boss_voit_toutes_les_entrees(): void
    {
        $this->actingAs($this->utilisateur(Role::BOSS))
            ->get('/dashboarde/dashboard')
            ->assertOk()
            ->assertSee('Utilisateurs')
            ->assertSee('Départements')
            ->assertSee('Témoignages')
            ->assertSee('Inscrire un utilisateur');
    }

    public function test_le_badge_compte_les_temoignages_en_attente(): void
    {
        foreach (range(1, 3) as $i) {
            Temoignage::create([
                'nom' => "Client $i",
                'service' => 'Design Graphique',
                'note' => 5,
                'message' => 'Un message de test suffisamment long pour la validation.',
            ]);
        }

        $this->actingAs($this->utilisateur(Role::BOSS))
            ->get('/dashboarde/dashboard')
            ->assertOk()
            ->assertSee('as-badge', false)
            ->assertSee('>3<', false);
    }

    public function test_chaque_lien_du_menu_est_accessible_a_celui_qui_le_voit(): void
    {
        $boss = $this->utilisateur(Role::BOSS);

        $routes = [
            'dashboard', 'viewproject', 'createproject', 'viewfile',
            'brouillonfiles', 'archivesfiles', 'utilisateurs', 'registerauth',
            'projects', 'files', 'roles', 'departements',
            'frontend-dashboard', 'temoignages', 'profileshower',
            'profilesessions', 'nouveautes', 'aide',
        ];

        foreach ($routes as $nom) {
            $this->actingAs($boss)
                ->get(route($nom))
                ->assertOk("La route « $nom » est visible dans le menu mais ne répond pas.");
        }
    }
}
