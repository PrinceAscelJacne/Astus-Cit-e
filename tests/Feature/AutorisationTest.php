<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\File;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Couvre les failles d'autorisation relevées lors de l'audit.
 */
class AutorisationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (Role::LIBELLES as $id => $libelle) {
            Role::create(['id' => $id, 'name' => $libelle]);
        }
    }

    private function utilisateur(int $roleId, ?int $departmentId = null): User
    {
        return User::factory()->create([
            'role_id' => $roleId,
            'department_id' => $departmentId,
        ]);
    }

    private function fichierDe(User $user): File
    {
        return File::create([
            'filename' => 'secret-' . $user->id . '.docx',
            'path' => 'files/secret-' . $user->id . '.docx',
            'type' => 'officiel',
            'status' => 'actif',
            'user_id' => $user->id,
        ]);
    }

    /* Dashboard du site vitrine : données personnelles de tiers */

    public function test_le_dashboard_des_rendez_vous_est_inaccessible_sans_authentification(): void
    {
        $this->get('/frontend/admin/dashboard')->assertRedirect('/login');
    }

    public function test_le_dashboard_des_rendez_vous_est_refuse_a_un_employe(): void
    {
        $this->actingAs($this->utilisateur(Role::EMPLOYE))
            ->get('/frontend/admin/dashboard')
            ->assertForbidden();
    }

    public function test_le_dashboard_des_rendez_vous_est_accessible_au_boss(): void
    {
        $this->actingAs($this->utilisateur(Role::BOSS))
            ->get('/frontend/admin/dashboard')
            ->assertOk();
    }

    /* Suppression par nom de table */

    public function test_la_suppression_refuse_une_table_hors_liste_blanche(): void
    {
        $boss = $this->utilisateur(Role::BOSS);

        $this->actingAs($boss)
            ->delete('/dashboarde/delete/migrations/1')
            ->assertNotFound();

        $this->assertGreaterThan(0, DB::table('migrations')->count());
    }

    public function test_la_suppression_refuse_la_table_des_sessions(): void
    {
        $this->actingAs($this->utilisateur(Role::BOSS))
            ->delete('/dashboarde/delete/sessions/1')
            ->assertNotFound();
    }

    public function test_un_employe_ne_peut_pas_atteindre_la_suppression(): void
    {
        $cible = $this->utilisateur(Role::EMPLOYE);

        $this->actingAs($this->utilisateur(Role::EMPLOYE))
            ->delete('/dashboarde/delete/users/' . $cible->id)
            ->assertForbidden();

        $this->assertNotNull(User::find($cible->id));
    }

    /* Cloisonnement des fichiers */

    public function test_un_utilisateur_ne_peut_pas_telecharger_le_fichier_d_un_autre(): void
    {
        $victime = $this->utilisateur(Role::EMPLOYE);
        $fichier = $this->fichierDe($victime);

        $this->actingAs($this->utilisateur(Role::EMPLOYE))
            ->get('/dashboarde/downloadfile/' . $fichier->id)
            ->assertForbidden();
    }

    public function test_un_utilisateur_ne_peut_pas_supprimer_le_fichier_d_un_autre(): void
    {
        $victime = $this->utilisateur(Role::EMPLOYE);
        $fichier = $this->fichierDe($victime);

        $this->actingAs($this->utilisateur(Role::EMPLOYE))
            ->delete('/dashboarde/deletefile/' . $fichier->id)
            ->assertForbidden();

        $this->assertNotNull(File::find($fichier->id));
    }

    public function test_un_utilisateur_ne_peut_pas_lire_le_contenu_du_fichier_d_un_autre(): void
    {
        $victime = $this->utilisateur(Role::EMPLOYE);
        $fichier = $this->fichierDe($victime);

        $this->actingAs($this->utilisateur(Role::EMPLOYE))
            ->get('/get-file-content/' . $fichier->filename)
            ->assertForbidden();
    }

    public function test_un_utilisateur_ne_peut_pas_ecraser_le_fichier_d_un_autre(): void
    {
        $victime = $this->utilisateur(Role::EMPLOYE);
        $fichier = $this->fichierDe($victime);

        $this->actingAs($this->utilisateur(Role::EMPLOYE))
            ->put('/dashboarde/updatefile', [
                'file_id' => $fichier->id,
                'file_content' => 'Contenu injecté',
                'type' => 'officiel',
            ])
            ->assertForbidden();

        $this->assertSame($fichier->path, File::find($fichier->id)->path);
    }

    /* Escalade de privilèges à l'inscription */

    public function test_un_employe_ne_peut_pas_ouvrir_le_formulaire_d_inscription(): void
    {
        $this->actingAs($this->utilisateur(Role::EMPLOYE))
            ->get('/auth/registerauth')
            ->assertForbidden();
    }

    public function test_un_chef_de_departement_ne_peut_pas_creer_un_boss(): void
    {
        $dep = Department::create(['name' => 'Comptabilité']);
        $chef = $this->utilisateur(Role::CHEF_DEPARTEMENT, $dep->id);

        $this->actingAs($chef)->post('/auth/storeauth', [
            'name' => 'Pirate',
            'firstname' => 'Test',
            'phone' => '12345678',
            'email' => 'pirate@example.com',
            'role' => Role::BOSS,
        ])->assertSessionHasErrors('role');

        $this->assertNull(User::where('email', 'pirate@example.com')->first());
    }

    public function test_un_chef_de_departement_cree_un_employe_dans_son_propre_departement(): void
    {
        $sien = Department::create(['name' => 'Comptabilité']);
        $autre = Department::create(['name' => 'Logistique']);
        $chef = $this->utilisateur(Role::CHEF_DEPARTEMENT, $sien->id);

        $this->actingAs($chef)->post('/auth/storeauth', [
            'name' => 'Nouveau',
            'firstname' => 'Employé',
            'phone' => '12345678',
            'email' => 'nouveau@example.com',
            // Département d'un autre service : doit être ignoré.
            'department_id' => $autre->id,
            'role' => Role::EMPLOYE,
        ])->assertSessionHasNoErrors();

        $cree = User::where('email', 'nouveau@example.com')->firstOrFail();

        $this->assertSame(Role::EMPLOYE, (int) $cree->role_id);
        $this->assertSame($sien->id, $cree->department_id);
    }

    public function test_le_mot_de_passe_n_est_plus_accepte_depuis_le_formulaire(): void
    {
        $boss = $this->utilisateur(Role::BOSS);

        $this->actingAs($boss)->post('/auth/storeauth', [
            'name' => 'Cible',
            'firstname' => 'Test',
            'phone' => '12345678',
            'email' => 'cible@example.com',
            'role' => Role::EMPLOYE,
            'password' => 'motdepasse-choisi-par-attaquant',
        ]);

        $cree = User::where('email', 'cible@example.com')->firstOrFail();

        // Le mot de passe est généré par le serveur : celui posté est ignoré.
        $this->assertFalse(password_verify('motdepasse-choisi-par-attaquant', $cree->password));
    }

    /* Cloisonnement des projets */

    public function test_un_projet_d_un_autre_departement_n_est_pas_modifiable(): void
    {
        $depA = Department::create(['name' => 'A']);
        $depB = Department::create(['name' => 'B']);

        $chefA = $this->utilisateur(Role::CHEF_DEPARTEMENT, $depA->id);
        $chefB = $this->utilisateur(Role::CHEF_DEPARTEMENT, $depB->id);

        $projet = Project::create([
            'name' => 'Projet de B',
            'description' => '',
            'department_id' => $depB->id,
            'user_id' => $chefB->id,
            'status' => 'actif',
        ]);

        $this->actingAs($chefA)
            ->get('/dashboarde/modifyproject/' . $projet->id)
            ->assertForbidden();
    }

    public function test_un_employe_ne_peut_pas_creer_de_projet(): void
    {
        $this->actingAs($this->utilisateur(Role::EMPLOYE))
            ->get('/dashboarde/createproject')
            ->assertForbidden();
    }

    public function test_la_recherche_ne_laisse_pas_fuiter_les_projets_d_un_autre_departement(): void
    {
        $depA = Department::create(['name' => 'A']);
        $depB = Department::create(['name' => 'B']);

        $employeA = $this->utilisateur(Role::EMPLOYE, $depA->id);
        $chefB = $this->utilisateur(Role::CHEF_DEPARTEMENT, $depB->id);

        Project::create([
            'name' => 'Confidentiel B',
            // Le terme recherché n'apparaît que dans la description : c'est
            // exactement le cas que le orWhere non groupé laissait passer.
            'description' => 'dossier sensible',
            'department_id' => $depB->id,
            'user_id' => $chefB->id,
            'status' => 'actif',
        ]);

        $reponse = $this->actingAs($employeA)
            ->get('/dashboarde/viewproject?search=sensible')
            ->assertOk();

        $reponse->assertDontSee('Confidentiel B');
    }

    /* Inscription publique */

    public function test_l_inscription_publique_est_desactivee(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', [
            'name' => 'Intrus',
            'email' => 'intrus@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertNotFound();

        $this->assertNull(User::where('email', 'intrus@example.com')->first());
    }
}
