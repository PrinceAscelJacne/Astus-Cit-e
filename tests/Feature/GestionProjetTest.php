<?php

namespace Tests\Feature;

use App\Models\Discussion;
use App\Models\File;
use App\Models\Project;
use App\Models\Role;
use App\Models\Tache;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GestionProjetTest extends TestCase
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
            'description' => '',
            'user_id' => $createur->id,
            'status' => 'actif',
        ]);
    }

    private function fichier(Project $projet, User $deposant, string $visibilite = 'equipe'): File
    {
        return File::create([
            'filename' => 'affiche-gala.psd',
            'path' => 'files/affiche-gala.psd',
            'mime' => 'image/vnd.adobe.photoshop',
            'taille' => 15728640,
            'type' => 'officiel',
            'status' => 'actif',
            'visibilite' => $visibilite,
            'project_id' => $projet->id,
            'user_id' => $deposant->id,
        ]);
    }

    /* Types de fichiers */

    public function test_les_formats_du_metier_sont_acceptes(): void
    {
        $acceptees = File::extensionsAutorisees();

        // Agence de communication et d'événementiel : visuels, vidéos,
        // maquettes. La liste d'origine n'en couvrait presque rien.
        foreach (['psd', 'ai', 'indd', 'mov', 'mkv', 'webp', 'heic', 'wav', 'pptx'] as $extension) {
            $this->assertContains($extension, $acceptees, "« $extension » devrait être accepté.");
        }
    }

    public function test_la_famille_du_fichier_est_reconnue(): void
    {
        $projet = $this->projet($this->utilisateur());
        $deposant = $this->utilisateur();

        $cas = [
            'visuel.psd' => 'maquette',
            'teaser.mov' => 'video',
            'photo.heic' => 'image',
            'jingle.wav' => 'audio',
            'devis.pdf' => 'document',
        ];

        foreach ($cas as $nom => $familleAttendue) {
            $fichier = $this->fichier($projet, $deposant);
            $fichier->filename = $nom;

            $this->assertSame($familleAttendue, $fichier->famille(), "$nom mal classé.");
        }
    }

    public function test_la_taille_est_affichee_lisiblement(): void
    {
        $fichier = $this->fichier($this->projet($this->utilisateur()), $this->utilisateur());

        $this->assertSame('15 Mo', $fichier->tailleLisible());
    }

    /* Visibilité — le point central */

    public function test_un_fichier_prive_n_est_vu_que_par_son_deposant(): void
    {
        $projet = $this->projet($this->utilisateur());
        $deposant = $this->utilisateur();
        $collegue = $this->utilisateur();

        $projet->membres()->attach([$deposant->id, $collegue->id]);
        $fichier = $this->fichier($projet, $deposant, 'prive');

        $this->assertTrue($fichier->visiblePar($deposant));
        $this->assertFalse($fichier->visiblePar($collegue->fresh()));
    }

    public function test_un_fichier_reserve_a_la_direction_echappe_a_l_equipe(): void
    {
        $projet = $this->projet($this->utilisateur());
        $deposant = $this->utilisateur();
        $collegue = $this->utilisateur();

        $projet->membres()->attach([$deposant->id, $collegue->id]);
        $fichier = $this->fichier($projet, $deposant, 'direction');

        $this->assertFalse($fichier->visiblePar($collegue->fresh()));
        $this->assertTrue($fichier->visiblePar($this->utilisateur(Role::BOSS)));
    }

    public function test_un_fichier_d_equipe_n_est_pas_vu_hors_du_projet(): void
    {
        $projet = $this->projet($this->utilisateur());
        $deposant = $this->utilisateur();
        $membre = $this->utilisateur();
        $etranger = $this->utilisateur();

        $projet->membres()->attach([$deposant->id, $membre->id]);
        $fichier = $this->fichier($projet, $deposant, 'equipe');

        $this->assertTrue($fichier->visiblePar($membre->fresh()));
        $this->assertFalse($fichier->visiblePar($etranger->fresh()));
    }

    public function test_un_fichier_ouvert_a_l_entreprise_est_vu_de_tous(): void
    {
        $projet = $this->projet($this->utilisateur());
        $fichier = $this->fichier($projet, $this->utilisateur(), 'entreprise');

        $this->assertTrue($fichier->visiblePar($this->utilisateur()));
    }

    public function test_le_boss_voit_tout_y_compris_le_prive(): void
    {
        $projet = $this->projet($this->utilisateur());
        $fichier = $this->fichier($projet, $this->utilisateur(), 'prive');

        // C'est la direction qui arbitre : elle doit pouvoir tout consulter.
        $this->assertTrue($fichier->visiblePar($this->utilisateur(Role::BOSS)));
    }

    public function test_la_direction_impose_une_visibilite_et_on_sait_qui(): void
    {
        $projet = $this->projet($this->utilisateur());
        $deposant = $this->utilisateur();
        $boss = $this->utilisateur(Role::BOSS);

        $fichier = $this->fichier($projet, $deposant, 'entreprise');
        $fichier->imposerVisibilite('direction', $boss);

        $fichier->refresh();

        $this->assertSame('direction', $fichier->visibilite);
        $this->assertSame($boss->id, $fichier->visibilite_imposee_par);
    }

    public function test_la_requete_filtree_donne_le_meme_resultat_que_la_regle(): void
    {
        $projet = $this->projet($this->utilisateur());
        $deposant = $this->utilisateur();
        $membre = $this->utilisateur();
        $projet->membres()->attach([$deposant->id, $membre->id]);

        $this->fichier($projet, $deposant, 'prive');
        $this->fichier($projet, $deposant, 'direction');
        $this->fichier($projet, $deposant, 'equipe');
        $this->fichier($projet, $deposant, 'entreprise');

        $membre = $membre->fresh();
        $visibles = File::visiblesPar($membre)->get();

        // La liste et le contrôle unitaire doivent concorder, sinon un fichier
        // restreint remonterait par un chemin détourné.
        $this->assertSame(2, $visibles->count());
        foreach ($visibles as $fichier) {
            $this->assertTrue($fichier->visiblePar($membre));
        }
    }

    /* Discussions */

    public function test_ce_qui_est_dit_reste_attache_au_projet(): void
    {
        $projet = $this->projet($this->utilisateur());
        $auteur = User::factory()->create(['name' => 'Kponou', 'surname' => 'Alice', 'role_id' => Role::EMPLOYE]);

        Discussion::create([
            'project_id' => $projet->id,
            'user_id' => $auteur->id,
            'corps' => 'Le logo doit être décliné en version blanche.',
        ]);

        $message = $projet->discussions()->firstOrFail();

        $this->assertSame('Alice Kponou', $message->auteur_nom);
        $this->assertStringContainsString('version blanche', $message->corps);
    }

    public function test_un_message_peut_porter_sur_un_fichier_precis(): void
    {
        $projet = $this->projet($this->utilisateur());
        $fichier = $this->fichier($projet, $this->utilisateur());

        Discussion::create([
            'project_id' => $projet->id,
            'user_id' => $this->utilisateur()->id,
            'file_id' => $fichier->id,
            'corps' => 'Cette affiche est trop sombre.',
        ]);

        $this->assertSame(1, $fichier->discussions()->count());
    }

    /* Tâches */

    public function test_une_tache_suit_son_avancement_et_son_auteur(): void
    {
        $projet = $this->projet($this->utilisateur());
        $graphiste = $this->utilisateur();

        $tache = Tache::create([
            'project_id' => $projet->id,
            'titre' => 'Décliner le logo en blanc',
            'assignee_id' => $graphiste->id,
            'echeance' => now()->addDays(3),
        ]);

        $this->assertSame('À faire', $tache->statutLisible());
        $this->assertFalse($tache->estEnRetard());

        $tache->marquerFaite($graphiste);
        $tache->refresh();

        $this->assertSame('fait', $tache->statut);
        $this->assertSame($graphiste->id, $tache->faite_par);
        $this->assertNotNull($tache->faite_le);
    }

    public function test_une_tache_en_retard_est_signalee(): void
    {
        $projet = $this->projet($this->utilisateur());

        $tache = Tache::create([
            'project_id' => $projet->id,
            'titre' => 'Livrer la vidéo',
            'echeance' => now()->subDays(2),
        ]);

        $this->assertTrue($tache->estEnRetard());
    }

    public function test_la_part_de_taches_faites_se_calcule(): void
    {
        $projet = $this->projet($this->utilisateur());

        $this->assertNull($projet->partTachesFaites());

        foreach (['a_faire', 'fait', 'fait', 'a_faire'] as $statut) {
            Tache::create(['project_id' => $projet->id, 'titre' => 'T', 'statut' => $statut]);
        }

        $this->assertSame(50, $projet->fresh()->partTachesFaites());
    }

    /* Archivage et reprise */

    public function test_un_projet_archive_conserve_tout_son_contenu(): void
    {
        $auteur = $this->utilisateur(Role::BOSS);
        $this->actingAs($auteur);

        $projet = $this->projet($auteur);
        $this->fichier($projet, $auteur);
        Discussion::create(['project_id' => $projet->id, 'user_id' => $auteur->id, 'corps' => 'Note']);
        Tache::create(['project_id' => $projet->id, 'titre' => 'Une tâche']);

        $projet->archiver();
        $projet->refresh();

        $this->assertTrue($projet->estArchive());
        // Rien ne disparaît : on doit pouvoir ressortir le projet plus tard,
        // pour le reprendre ou le présenter à un client.
        $this->assertSame(1, $projet->files()->count());
        $this->assertSame(1, $projet->discussions()->count());
        $this->assertSame(1, $projet->taches()->count());
    }

    public function test_un_projet_se_ressort_des_archives(): void
    {
        $auteur = $this->utilisateur(Role::BOSS);
        $this->actingAs($auteur);

        $projet = $this->projet($auteur);
        $fichier = $this->fichier($projet, $auteur);

        $projet->archiver();
        $projet->refresh();
        $this->assertSame('archive', $fichier->fresh()->status);

        $projet->desarchiver();
        $projet->refresh();

        $this->assertFalse($projet->estArchive());
        $this->assertSame('actif', $fichier->fresh()->status);
        $this->assertDatabaseHas('activites', ['action' => 'restaure', 'project_id' => $projet->id]);
    }
}
