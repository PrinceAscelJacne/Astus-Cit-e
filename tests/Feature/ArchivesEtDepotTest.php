<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Deux comportements que l'on croyait acquis et qui ne l'étaient pas :
 * retrouver un projet une fois archivé, et voir un fichier déposé.
 */
class ArchivesEtDepotTest extends TestCase
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

    private function projet(User $createur, string $nom = 'Gala de fin d\'année'): Project
    {
        return Project::create([
            'name' => $nom, 'description' => '',
            'user_id' => $createur->id, 'status' => 'actif',
        ]);
    }

    /* Archives */

    public function test_un_projet_archive_apparait_dans_les_archives(): void
    {
        $boss = $this->utilisateur(Role::BOSS);
        $projet = $this->projet($boss, 'Salon du numérique');

        $this->actingAs($boss)
            ->post(route('projet.archiver', $projet))
            ->assertRedirect();

        // La page ne recevait aucune donnée : le projet disparaissait de la
        // liste sans réapparaître nulle part.
        $this->actingAs($boss)
            ->get(route('archives'))
            ->assertOk()
            ->assertSee('Salon du numérique');
    }

    public function test_un_projet_actif_n_apparait_pas_dans_les_archives(): void
    {
        $boss = $this->utilisateur(Role::BOSS);
        $this->projet($boss, 'Projet toujours en cours');

        $this->actingAs($boss)
            ->get(route('archives'))
            ->assertOk()
            ->assertDontSee('Projet toujours en cours');
    }

    public function test_les_archives_respectent_le_perimetre_de_chacun(): void
    {
        $boss = $this->utilisateur(Role::BOSS);
        $departement = \App\Models\Department::create(['name' => 'Création']);
        $autre = \App\Models\Department::create(['name' => 'Commercial']);

        $projet = $this->projet($boss, 'Confidentiel Commercial');
        $projet->update(['department_id' => $autre->id]);
        $projet->archiver();

        $employe = User::factory()->create([
            'role_id' => Role::EMPLOYE,
            'department_id' => $departement->id,
        ]);

        $this->actingAs($employe)
            ->get(route('archives'))
            ->assertOk()
            ->assertDontSee('Confidentiel Commercial');
    }

    public function test_on_ressort_un_projet_depuis_les_archives(): void
    {
        $boss = $this->utilisateur(Role::BOSS);
        $projet = $this->projet($boss);
        $projet->archiver();

        $this->actingAs($boss)
            ->get(route('archives'))
            ->assertOk()
            ->assertSee(route('projet.fiche', $projet), false);

        $this->actingAs($boss)
            ->post(route('projet.desarchiver', $projet))
            ->assertRedirect();

        $this->assertFalse($projet->fresh()->estArchive());
    }

    /* Dépôt d'un livrable */

    public function test_un_fichier_depose_depuis_le_projet_y_apparait(): void
    {
        Storage::fake('local');

        $boss = $this->utilisateur(Role::BOSS);
        $projet = $this->projet($boss);

        $this->actingAs($boss)
            ->post(route('projet.livrable', $projet), [
                'fichier' => UploadedFile::fake()->image('affiche.png'),
                'visibilite' => 'equipe',
            ])
            ->assertRedirect();

        $fichier = File::firstOrFail();

        // Le rattachement est implicite : c'est précisément ce qui manquait,
        // le formulaire général étant sur « Aucun projet » par défaut.
        $this->assertSame($projet->id, $fichier->project_id);
        $this->assertSame('affiche.png', $fichier->filename);
        $this->assertNotNull($fichier->taille);

        $this->actingAs($boss)
            ->get(route('projet.fiche', $projet))
            ->assertOk()
            ->assertSee('affiche.png');
    }

    public function test_le_deposant_choisit_qui_voit_son_livrable(): void
    {
        Storage::fake('local');

        $deposant = $this->utilisateur();
        $projet = $this->projet($deposant);
        $collegue = $this->utilisateur();
        $projet->membres()->attach([$deposant->id, $collegue->id]);

        $this->actingAs($deposant)
            ->post(route('projet.livrable', $projet), [
                'fichier' => UploadedFile::fake()->create('note-perso.txt', 10),
                'visibilite' => 'prive',
            ])
            ->assertRedirect();

        $this->actingAs($collegue->fresh())
            ->get(route('projet.fiche', $projet))
            ->assertOk()
            ->assertDontSee('note-perso.txt');
    }

    public function test_un_livrage_depose_dans_un_projet_archive_reste_range(): void
    {
        Storage::fake('local');

        $boss = $this->utilisateur(Role::BOSS);
        $projet = $this->projet($boss);
        $projet->archiver();

        $this->actingAs($boss)
            ->post(route('projet.livrable', $projet->fresh()), [
                'fichier' => UploadedFile::fake()->image('complement.jpg'),
            ])
            ->assertRedirect();

        // Sinon le fichier réapparaîtrait dans les listes actives alors que
        // son projet est rangé.
        $this->assertSame('archive', File::firstOrFail()->status);
    }

    public function test_un_format_metier_est_accepte_au_depot(): void
    {
        Storage::fake('local');

        $boss = $this->utilisateur(Role::BOSS);
        $projet = $this->projet($boss);

        $this->actingAs($boss)
            ->post(route('projet.livrable', $projet), [
                'fichier' => UploadedFile::fake()->create('maquette.psd', 2048),
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertSame('maquette.psd', File::firstOrFail()->filename);
    }
}
