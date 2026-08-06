<?php

namespace Tests\Feature;

use App\Models\Activite;
use App\Models\Department;
use App\Models\File;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use App\Support\Droits;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DroitsEtTracabiliteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    private function utilisateur(int $roleId = Role::EMPLOYE): User
    {
        return User::factory()->create(['role_id' => $roleId]);
    }

    private function projet(User $createur): Project
    {
        return Project::create([
            'name' => 'Refonte identité',
            'description' => '',
            'user_id' => $createur->id,
            'status' => 'actif',
        ]);
    }

    /* Droits : rôle, dérogation, Boss */

    public function test_le_boss_a_tous_les_droits_sans_condition(): void
    {
        $boss = $this->utilisateur(Role::BOSS);

        foreach (Droits::toutes() as $droit) {
            $this->assertTrue($boss->peut($droit), "Le Boss devrait avoir « $droit ».");
        }
    }

    public function test_un_employe_n_a_aucun_droit_par_defaut(): void
    {
        $employe = $this->utilisateur();

        $this->assertFalse($employe->peut(Droits::MESSAGES));
        $this->assertFalse($employe->peut(Droits::PROJETS_CREER));
    }

    public function test_le_droit_vient_du_role(): void
    {
        $chef = $this->utilisateur(Role::CHEF_DEPARTEMENT);

        $this->assertTrue($chef->peut(Droits::PROJETS_CREER));
        $this->assertFalse($chef->peut(Droits::UTILISATEURS));
    }

    public function test_une_derogation_accorde_un_droit_que_le_role_n_a_pas(): void
    {
        $employe = $this->utilisateur();
        $employe->derogations()->create(['droit' => Droits::MESSAGES, 'accorde' => true]);
        $employe->load('derogations');

        $this->assertTrue($employe->peut(Droits::MESSAGES));
    }

    public function test_une_derogation_retire_un_droit_que_le_role_donne(): void
    {
        $chef = $this->utilisateur(Role::CHEF_DEPARTEMENT);
        $this->assertTrue($chef->peut(Droits::PROJETS_CREER));

        $chef->derogations()->create(['droit' => Droits::PROJETS_CREER, 'accorde' => false]);
        $chef->load('derogations');

        // La dérogation l'emporte dans les deux sens : c'est ce qui permet de
        // retirer un droit à une personne sans toucher au rôle.
        $this->assertFalse($chef->peut(Droits::PROJETS_CREER));
    }

    public function test_un_role_sur_mesure_porte_ses_propres_droits(): void
    {
        $graphiste = Role::create(['name' => 'Graphiste', 'description' => 'Création visuelle']);
        $graphiste->definirDroits([Droits::PROJETS_CREER, Droits::TEMOIGNAGES]);

        $utilisateur = User::factory()->create(['role_id' => $graphiste->id]);

        $this->assertTrue($utilisateur->peut(Droits::PROJETS_CREER));
        $this->assertTrue($utilisateur->peut(Droits::TEMOIGNAGES));
        $this->assertFalse($utilisateur->peut(Droits::UTILISATEURS));
    }

    public function test_un_droit_inconnu_est_ignore(): void
    {
        $role = Role::create(['name' => 'Test']);
        $role->definirDroits([Droits::MESSAGES, 'droit.inexistant']);

        $this->assertSame(1, $role->droits()->count());
    }

    public function test_un_role_systeme_n_est_pas_supprimable(): void
    {
        $this->assertFalse(Role::find(Role::BOSS)->estSupprimable());

        $surMesure = Role::create(['name' => 'Temporaire']);
        $this->assertTrue($surMesure->estSupprimable());
    }

    /* Équipe projet */

    public function test_plusieurs_personnes_travaillent_sur_un_meme_projet(): void
    {
        $chef = $this->utilisateur(Role::CHEF_DEPARTEMENT);
        $projet = $this->projet($chef);

        $a = $this->utilisateur();
        $b = $this->utilisateur();

        $projet->membres()->attach($a->id, ['role_projet' => 'responsable', 'ajoute_par' => $chef->id]);
        $projet->membres()->attach($b->id, ['role_projet' => 'contributeur', 'ajoute_par' => $chef->id]);

        $this->assertSame(2, $projet->membres()->count());
        $this->assertSame(1, $projet->responsables()->count());
        $this->assertTrue($projet->fresh()->estMembre($a));
    }

    public function test_l_avancement_se_lit_et_se_chiffre(): void
    {
        $projet = $this->projet($this->utilisateur());

        $this->assertSame('À faire', $projet->avancementLisible());
        $this->assertSame(0, $projet->avancementPourcent());

        $projet->update(['avancement' => 'termine']);

        $this->assertSame('Terminé', $projet->fresh()->avancementLisible());
        $this->assertSame(100, $projet->fresh()->avancementPourcent());
    }

    public function test_un_projet_depasse_son_echeance(): void
    {
        $projet = $this->projet($this->utilisateur());

        $projet->update(['echeance' => now()->subDay(), 'avancement' => 'en_cours']);
        $this->assertTrue($projet->fresh()->estEnRetard());

        $projet->update(['avancement' => 'termine']);
        $this->assertFalse($projet->fresh()->estEnRetard());
    }

    /* Traçabilité */

    public function test_la_creation_d_un_projet_est_consignee(): void
    {
        $auteur = $this->utilisateur(Role::CHEF_DEPARTEMENT);
        $this->actingAs($auteur);

        $projet = $this->projet($auteur);

        $activite = Activite::where('sujet_type', Project::class)->firstOrFail();

        $this->assertSame('cree', $activite->action);
        $this->assertSame($auteur->id, $activite->user_id);
        $this->assertSame('Refonte identité', $activite->sujet_libelle);
        $this->assertSame($projet->id, $activite->project_id);
    }

    public function test_la_modification_retient_l_avant_et_l_apres(): void
    {
        $auteur = $this->utilisateur(Role::BOSS);
        $this->actingAs($auteur);

        $projet = $this->projet($auteur);
        $projet->update(['name' => 'Nouvelle identité']);

        $activite = Activite::where('action', 'modifie')->firstOrFail();

        $this->assertSame('Refonte identité', $activite->details['name']['avant']);
        $this->assertSame('Nouvelle identité', $activite->details['name']['apres']);
    }

    public function test_l_archivage_est_consigne_comme_tel(): void
    {
        $this->actingAs($this->utilisateur(Role::BOSS));

        $projet = $this->projet($this->utilisateur());
        $projet->update(['status' => 'archive']);

        // Sans distinction, l'archivage passerait pour une simple modification
        // et deviendrait invisible dans le journal.
        $this->assertDatabaseHas('activites', ['action' => 'archive']);
    }

    public function test_la_suppression_est_consignee(): void
    {
        $this->actingAs($this->utilisateur(Role::BOSS));

        $projet = $this->projet($this->utilisateur());
        $projet->delete();

        $this->assertDatabaseHas('activites', ['action' => 'supprime']);
    }

    public function test_le_journal_ne_conserve_jamais_les_secrets(): void
    {
        $this->actingAs($this->utilisateur(Role::BOSS));

        $cible = $this->utilisateur();
        $cible->update([
            'password' => bcrypt('nouveau-mot-de-passe'),
            'phone' => '99887766',
        ]);

        $activite = Activite::where('sujet_type', User::class)
            ->where('action', 'modifie')
            ->latest('id')
            ->firstOrFail();

        $this->assertArrayNotHasKey('password', $activite->details);
        $this->assertArrayHasKey('phone', $activite->details);
    }

    public function test_le_nom_de_l_auteur_survit_a_la_suppression_du_compte(): void
    {
        $auteur = User::factory()->create([
            'role_id' => Role::BOSS,
            'name' => 'Dupont',
            'surname' => 'Jean',
        ]);
        $this->actingAs($auteur);

        $this->projet($auteur);

        $activite = Activite::where('sujet_type', Project::class)->firstOrFail();

        // Le nom est recopié : le journal doit rester lisible même après
        // suppression du compte.
        $this->assertSame('Jean Dupont', $activite->auteur_nom);
    }

    public function test_un_fichier_est_rattache_a_son_projet_dans_le_journal(): void
    {
        $auteur = $this->utilisateur(Role::BOSS);
        $this->actingAs($auteur);

        $projet = $this->projet($auteur);

        File::create([
            'filename' => 'maquette.docx',
            'path' => 'files/maquette.docx',
            'type' => 'officiel',
            'status' => 'actif',
            'project_id' => $projet->id,
            'user_id' => $auteur->id,
        ]);

        $activite = Activite::where('sujet_type', File::class)->firstOrFail();

        $this->assertSame($projet->id, $activite->project_id);
        $this->assertSame('maquette.docx', $activite->sujet_libelle);
        $this->assertStringContainsString('maquette.docx', $activite->resume());
    }

    public function test_le_journal_d_un_projet_regroupe_ses_evenements(): void
    {
        $auteur = $this->utilisateur(Role::BOSS);
        $this->actingAs($auteur);

        $projet = $this->projet($auteur);
        $projet->update(['avancement' => 'en_cours']);

        File::create([
            'filename' => 'brief.docx', 'path' => 'files/brief.docx',
            'type' => 'officiel', 'status' => 'actif',
            'project_id' => $projet->id, 'user_id' => $auteur->id,
        ]);

        // Création du projet, modification, création du fichier.
        $this->assertSame(3, $projet->journal()->count());
    }
}
