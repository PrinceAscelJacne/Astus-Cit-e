<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use App\Models\File;
use App\Models\Project;
use App\Models\Tache;
use App\Models\User;
use App\Support\Droits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Espace de travail d'un projet : livrables, échanges, tâches, équipe et
 * journal réunis sur une seule page.
 *
 * C'est ici que le travail à distance se fait : chacun dépose ce qu'il
 * produit, écrit ce qu'il a à dire, et voit ce qui reste à faire.
 */
class EspaceProjetController extends Controller
{
    /**
     * Accès à l'espace : membre de l'équipe, créateur, ou droit de gérer
     * tous les projets.
     */
    private function verifierAcces(Project $projet): User
    {
        $utilisateur = Auth::user();

        $autorise = $projet->estMembre($utilisateur)
            || $utilisateur->isBoss()
            || $utilisateur->peut(Droits::PROJETS_GERER_TOUS);

        abort_unless($autorise, 403, "Vous ne faites pas partie de ce projet.");

        return $utilisateur;
    }

    public function fiche(Project $projet)
    {
        $utilisateur = $this->verifierAcces($projet);

        $projet->load(['membres', 'department', 'createur']);

        return view('dashboard.pages.project.fiche', [
            'projet' => $projet,
            // Les livrables passent par le filtre de visibilité : un fichier
            // privé ou réservé à la direction ne doit pas remonter ici.
            'livrables' => $projet->files()
                ->visiblesPar($utilisateur)
                ->with('user')
                ->latest()
                ->get(),
            'discussions' => $projet->discussions()->with('auteur', 'file')->get(),
            'taches' => $projet->taches()->with('assignee')->latest()->get(),
            'journal' => $this->journalVisiblePar($projet, $utilisateur),
            'candidats' => User::orderBy('surname')->get(),
            'peutGerer' => $utilisateur->isBoss()
                || $utilisateur->peut(Droits::PROJETS_GERER_TOUS)
                || (int) $projet->user_id === (int) $utilisateur->id,
        ]);
    }

    /**
     * Journal du projet, amputé de ce que la personne n'a pas le droit de voir.
     *
     * Une entrée porte le nom de son sujet : sans ce filtre, le journal
     * révélait le nom des fichiers privés ou réservés à la direction, alors
     * même qu'ils sont absents de la liste des livrables.
     */
    private function journalVisiblePar(Project $projet, User $utilisateur)
    {
        $entrees = $projet->journal()->with('auteur')->limit(60)->get();

        $fichiersVisibles = $projet->files()
            ->visiblesPar($utilisateur)
            ->pluck('id')
            ->all();

        return $entrees->reject(function ($entree) use ($fichiersVisibles) {
            return $entree->sujet_type === File::class
                && ! in_array((int) $entree->sujet_id, $fichiersVisibles, true);
        })->take(30)->values();
    }

    /**
     * Dépôt d'un livrable depuis le projet lui-même.
     *
     * Le formulaire général de la page « Fichiers » propose un choix de projet
     * dont la valeur par défaut est « Aucun » : un fichier déposé sans y
     * penser n'était rattaché à rien, et n'apparaissait donc nulle part dans
     * le projet. Ici le rattachement est implicite, il ne peut pas être oublié.
     */
    public function deposer(Request $request, Project $projet)
    {
        $utilisateur = $this->verifierAcces($projet);

        $donnees = $request->validate([
            'fichier' => 'required|file|mimes:' . implode(',', File::extensionsAutorisees()) . '|max:524288',
            'visibilite' => 'nullable|in:' . implode(',', array_keys(File::VISIBILITES)),
            'description' => 'nullable|string|max:255',
        ], [
            'fichier.required' => 'Choisissez un fichier à déposer.',
            'fichier.mimes' => 'Ce format n\'est pas accepté.',
            'fichier.max' => 'Le fichier ne peut pas dépasser 512 Mo.',
        ]);

        $depose = $request->file('fichier');

        File::create([
            'filename' => $depose->getClientOriginalName(),
            'path' => $depose->store('files'),
            'mime' => $depose->getClientMimeType(),
            'taille' => $depose->getSize(),
            'description' => $donnees['description'] ?? null,
            'type' => 'officiel',
            'status' => $projet->estArchive() ? 'archive' : 'actif',
            'visibilite' => $donnees['visibilite'] ?? 'equipe',
            'project_id' => $projet->id,
            'user_id' => $utilisateur->id,
        ]);

        return redirect()->back()->with('success', 'Livrable déposé sur le projet.');
    }

    /* Échanges */

    public function ecrire(Request $request, Project $projet)
    {
        $utilisateur = $this->verifierAcces($projet);

        $donnees = $request->validate([
            'corps' => 'required|string|max:3000',
            'file_id' => 'nullable|integer|exists:files,id',
        ], [
            'corps.required' => 'Votre message est vide.',
        ]);

        Discussion::create([
            'project_id' => $projet->id,
            'user_id' => $utilisateur->id,
            'corps' => $donnees['corps'],
            'file_id' => $donnees['file_id'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Message publié.');
    }

    /* Tâches */

    public function ajouterTache(Request $request, Project $projet)
    {
        $utilisateur = $this->verifierAcces($projet);

        $donnees = $request->validate([
            'titre' => 'required|string|max:200',
            'description' => 'nullable|string|max:2000',
            'assignee_id' => 'nullable|integer|exists:users,id',
            'echeance' => 'nullable|date',
        ], [
            'titre.required' => 'Indiquez ce qu\'il y a à faire.',
        ]);

        Tache::create($donnees + [
            'project_id' => $projet->id,
            'cree_par' => $utilisateur->id,
        ]);

        return redirect()->back()->with('success', 'Tâche ajoutée.');
    }

    public function basculerTache(Project $projet, Tache $tache)
    {
        $utilisateur = $this->verifierAcces($projet);

        abort_unless((int) $tache->project_id === (int) $projet->id, 404);

        if ($tache->statut === 'fait') {
            $tache->update(['statut' => 'a_faire', 'faite_le' => null, 'faite_par' => null]);
        } else {
            // Retient qui a terminé la tâche : sans cela on sait ce qui est
            // fait, jamais par qui.
            $tache->marquerFaite($utilisateur);
        }

        return redirect()->back();
    }

    /* Équipe */

    public function ajouterMembre(Request $request, Project $projet)
    {
        $utilisateur = $this->verifierAcces($projet);
        $this->exigerGestion($projet, $utilisateur);

        $donnees = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'role_projet' => 'required|in:responsable,contributeur',
        ]);

        if ($projet->membres()->where('users.id', $donnees['user_id'])->exists()) {
            return redirect()->back()->with('error', 'Cette personne fait déjà partie du projet.');
        }

        $projet->membres()->attach($donnees['user_id'], [
            'role_projet' => $donnees['role_projet'],
            'ajoute_par' => $utilisateur->id,
        ]);

        $projet->consigner('ajoute_equipe', [
            'membre' => User::find($donnees['user_id'])?->email,
            'role_projet' => $donnees['role_projet'],
        ]);

        return redirect()->back()->with('success', 'Membre ajouté au projet.');
    }

    public function retirerMembre(Project $projet, User $membre)
    {
        $utilisateur = $this->verifierAcces($projet);
        $this->exigerGestion($projet, $utilisateur);

        $projet->membres()->detach($membre->id);
        $projet->consigner('retire_equipe', ['membre' => $membre->email]);

        return redirect()->back()->with('success', 'Membre retiré du projet.');
    }

    /* Avancement et archivage */

    public function changerAvancement(Request $request, Project $projet)
    {
        $utilisateur = $this->verifierAcces($projet);

        $donnees = $request->validate([
            'avancement' => 'required|in:' . implode(',', array_keys(Project::AVANCEMENTS)),
            'echeance' => 'nullable|date',
        ]);

        $projet->update($donnees);

        return redirect()->back()->with('success', 'Avancement mis à jour.');
    }

    public function archiver(Project $projet)
    {
        $utilisateur = $this->verifierAcces($projet);
        $this->exigerGestion($projet, $utilisateur);

        $projet->archiver();

        return redirect()->route('viewproject')->with('success', 'Projet archivé. Il reste consultable dans les archives.');
    }

    public function desarchiver(Project $projet)
    {
        $utilisateur = $this->verifierAcces($projet);
        $this->exigerGestion($projet, $utilisateur);

        $projet->desarchiver();

        return redirect()->route('projet.fiche', $projet)->with('success', 'Projet ressorti des archives.');
    }

    /* Visibilité d'un livrable */

    public function changerVisibilite(Request $request, Project $projet, File $fichier)
    {
        $utilisateur = $this->verifierAcces($projet);

        abort_unless((int) $fichier->project_id === (int) $projet->id, 404);

        $donnees = $request->validate([
            'visibilite' => 'required|in:' . implode(',', array_keys(File::VISIBILITES)),
        ]);

        $estDirection = $utilisateur->isBoss() || $utilisateur->peut(Droits::FICHIERS_GERER_TOUS);
        $estDeposant = (int) $fichier->user_id === (int) $utilisateur->id;

        abort_unless($estDirection || $estDeposant, 403);

        if ($estDirection && ! $estDeposant) {
            // La direction tranche à la place du déposant : on retient qui.
            $fichier->imposerVisibilite($donnees['visibilite'], $utilisateur);
        } else {
            $fichier->update(['visibilite' => $donnees['visibilite']]);
        }

        return redirect()->back()->with('success', 'Visibilité mise à jour.');
    }

    private function exigerGestion(Project $projet, User $utilisateur): void
    {
        $peut = $utilisateur->isBoss()
            || $utilisateur->peut(Droits::PROJETS_GERER_TOUS)
            || (int) $projet->user_id === (int) $utilisateur->id
            || $projet->membres()
                ->where('users.id', $utilisateur->id)
                ->wherePivot('role_projet', 'responsable')
                ->exists();

        abort_unless($peut, 403, 'Seul un responsable du projet peut faire cela.');
    }
}
