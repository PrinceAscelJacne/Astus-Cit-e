<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\Project;
use App\Models\Role;
use App\Models\Tache;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EspaceProjetTest extends TestCase
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
            'name' => 'Gala de fin d\'année',
            'description' => 'Affiches, vidéo et signalétique.',
            'user_id' => $createur->id,
            'status' => 'actif',
        ]);
    }

    /* Accès */

    public function test_un_membre_de_l_equipe_accede_a_l_espace(): void
    {
        $projet = $this->projet($this->utilisateur());
        $membre = $this->utilisateur();
        $projet->membres()->attach($membre->id);

        $this->actingAs($membre)
            ->get(route('projet.fiche', $projet))
            ->assertOk()
            ->assertSee('Gala de fin d\'année');
    }

    public function test_une_personne_hors_du_projet_est_refusee(): void
    {
        $projet = $this->projet($this->utilisateur());

        // Être employé de l'entreprise ne suffit pas : il faut faire partie
        // de l'équipe du projet.
        $this->actingAs($this->utilisateur())
            ->get(route('projet.fiche', $projet))
            ->assertForbidden();
    }

    public function test_le_boss_accede_a_tout_projet(): void
    {
        $projet = $this->projet($this->utilisateur());

        $this->actingAs($this->utilisateur(Role::BOSS))
            ->get(route('projet.fiche', $projet))
            ->assertOk();
    }

    /* Livrables et visibilité */

    public function test_l_espace_masque_les_livrables_non_visibles(): void
    {
        $createur = $this->utilisateur();
        $projet = $this->projet($createur);
        $membre = $this->utilisateur();
        $projet->membres()->attach([$createur->id, $membre->id]);

        foreach (['equipe' => 'partage.png', 'prive' => 'brouillon-perso.psd'] as $visibilite => $nom) {
            File::create([
                'filename' => $nom, 'path' => 'files/' . $nom,
                'type' => 'officiel', 'status' => 'actif',
                'visibilite' => $visibilite,
                'project_id' => $projet->id, 'user_id' => $createur->id,
            ]);
        }

        $this->actingAs($membre->fresh())
            ->get(route('projet.fiche', $projet))
            ->assertOk()
            ->assertSee('partage.png')
            ->assertDontSee('brouillon-perso.psd');
    }

    public function test_la_direction_impose_la_visibilite_d_un_livrable(): void
    {
        $deposant = $this->utilisateur();
        $projet = $this->projet($deposant);
        $projet->membres()->attach($deposant->id);
        $boss = $this->utilisateur(Role::BOSS);

        $fichier = File::create([
            'filename' => 'contrat.pdf', 'path' => 'files/contrat.pdf',
            'type' => 'officiel', 'status' => 'actif', 'visibilite' => 'entreprise',
            'project_id' => $projet->id, 'user_id' => $deposant->id,
        ]);

        $this->actingAs($boss)
            ->post(route('projet.fichier.visibilite', [$projet, $fichier]), ['visibilite' => 'direction'])
            ->assertRedirect();

        $fichier->refresh();

        $this->assertSame('direction', $fichier->visibilite);
        $this->assertSame($boss->id, $fichier->visibilite_imposee_par);
    }

    /* Échanges */

    public function test_un_membre_ecrit_dans_le_fil_du_projet(): void
    {
        $projet = $this->projet($this->utilisateur());
        $membre = $this->utilisateur();
        $projet->membres()->attach($membre->id);

        $this->actingAs($membre)
            ->post(route('projet.message', $projet), ['corps' => 'Le logo doit être décliné en blanc.'])
            ->assertRedirect();

        $this->assertDatabaseHas('discussions', [
            'project_id' => $projet->id,
            'corps' => 'Le logo doit être décliné en blanc.',
        ]);
    }

    public function test_une_personne_hors_projet_ne_peut_pas_ecrire(): void
    {
        $projet = $this->projet($this->utilisateur());

        $this->actingAs($this->utilisateur())
            ->post(route('projet.message', $projet), ['corps' => 'Message indésirable.'])
            ->assertForbidden();

        $this->assertSame(0, $projet->discussions()->count());
    }

    /* Tâches */

    public function test_une_tache_s_ajoute_et_se_termine(): void
    {
        $projet = $this->projet($this->utilisateur());
        $membre = $this->utilisateur();
        $projet->membres()->attach($membre->id);

        $this->actingAs($membre)
            ->post(route('projet.tache', $projet), ['titre' => 'Décliner le logo en blanc'])
            ->assertRedirect();

        $tache = Tache::firstOrFail();
        $this->assertSame('a_faire', $tache->statut);

        $this->actingAs($membre)
            ->post(route('projet.tache.bascule', [$projet, $tache]))
            ->assertRedirect();

        $tache->refresh();
        $this->assertSame('fait', $tache->statut);
        $this->assertSame($membre->id, $tache->faite_par);
    }

    /* Équipe */

    public function test_le_responsable_compose_l_equipe(): void
    {
        $createur = $this->utilisateur(Role::CHEF_DEPARTEMENT);
        $projet = $this->projet($createur);
        $graphiste = $this->utilisateur();

        $this->actingAs($createur)
            ->post(route('projet.membre', $projet), [
                'user_id' => $graphiste->id,
                'role_projet' => 'contributeur',
            ])
            ->assertRedirect();

        $this->assertTrue($projet->fresh()->membres->contains('id', $graphiste->id));
        $this->assertDatabaseHas('activites', ['action' => 'ajoute_equipe']);
    }

    public function test_un_simple_contributeur_ne_compose_pas_l_equipe(): void
    {
        $projet = $this->projet($this->utilisateur());
        $contributeur = $this->utilisateur();
        $projet->membres()->attach($contributeur->id, ['role_projet' => 'contributeur']);

        $this->actingAs($contributeur)
            ->post(route('projet.membre', $projet), [
                'user_id' => $this->utilisateur()->id,
                'role_projet' => 'contributeur',
            ])
            ->assertForbidden();
    }

    /* Archivage et reprise */

    public function test_le_projet_s_archive_puis_se_ressort(): void
    {
        $createur = $this->utilisateur(Role::CHEF_DEPARTEMENT);
        $projet = $this->projet($createur);

        $this->actingAs($createur)
            ->post(route('projet.archiver', $projet))
            ->assertRedirect(route('viewproject'));

        $this->assertTrue($projet->fresh()->estArchive());

        $this->actingAs($createur)
            ->post(route('projet.desarchiver', $projet))
            ->assertRedirect();

        $this->assertFalse($projet->fresh()->estArchive());
    }

    public function test_l_avancement_se_change_depuis_l_espace(): void
    {
        $projet = $this->projet($this->utilisateur());
        $membre = $this->utilisateur();
        $projet->membres()->attach($membre->id);

        $this->actingAs($membre)
            ->post(route('projet.avancement', $projet), ['avancement' => 'en_relecture'])
            ->assertRedirect();

        $this->assertSame('en_relecture', $projet->fresh()->avancement);
    }
}
