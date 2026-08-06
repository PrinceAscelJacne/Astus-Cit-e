<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Role;
use App\Models\User;
use App\Models\Project;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TablesController extends Controller
{
    /**
     * Tables sur lesquelles une suppression est autorisée, associées au modèle
     * qui doit la porter. Toute valeur absente de cette liste est refusée :
     * le nom de la table provient de l'URL et ne doit jamais atteindre le
     * query builder tel quel.
     *
     * @var array<string, class-string<\Illuminate\Database\Eloquent\Model>>
     */
    private const TABLES_SUPPRIMABLES = [
        'files' => File::class,
        'projects' => Project::class,
        'departments' => Department::class,
        'users' => User::class,
    ];

    public function utilisateurs()
    {
        $users = User::with('role', 'department')->paginate(25);

        return view('dashboard.pages.tables.user', compact('users'));
    }

    public function roles()
    {
        $roles = Role::all();

        return view('dashboard.pages.tables.role', compact('roles'));
    }

    public function departements()
    {
        $deps = Department::all();

        return view('dashboard.pages.tables.dep', compact('deps'));
    }

    public function createdep(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
        ], [
            'nom.required' => 'Veuillez renseigner le nom du département.',
            'nom.max' => 'Le nom du département est trop long.',
        ]);

        $nom = trim($request->input('nom'));

        // Comparaison insensible à la casse, déléguée à la base plutôt qu'à
        // une boucle PHP sur l'intégralité de la table.
        $existe = Department::whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($nom)])->exists();

        if ($existe) {
            return redirect()->back()->with('error', 'Ce département existe déjà');
        }

        Department::create(['name' => $nom]);

        return redirect()->back()->with('success', 'Le département a été créé avec succès');
    }

    public function files(Request $request)
    {
        $query = File::with('user', 'project')->where('type', '!=', 'brouillon');

        if ($request->filled('search')) {
            // La colonne s'appelle « filename » : « name » n'existe pas sur
            // cette table et faisait échouer la requête.
            $query->where('filename', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('department')) {
            $query->whereHas('project', function ($q) use ($request) {
                $q->where('department_id', $request->department);
            });
        }

        if ($request->filled('project')) {
            $query->where('project_id', $request->project);
        }

        $files = $query->paginate(25)->withQueryString();
        $dep = Department::all();
        $projects = Project::all();

        return view('dashboard.pages.tables.file', compact('files', 'dep', 'projects'));
    }

    public function projects(Request $request)
    {
        $query = Project::with('department');

        if ($request->filled('search')) {
            $recherche = '%' . $request->search . '%';

            // Les deux conditions sont groupées : sans cela, le orWhere
            // s'appliquait au niveau supérieur et annulait les filtres.
            $query->where(function ($q) use ($recherche) {
                $q->where('name', 'like', $recherche)
                  ->orWhere('description', 'like', $recherche);
            });
        }

        $projects = $query->paginate(25)->withQueryString();

        return view('dashboard.pages.tables.project', compact('projects'));
    }

    /**
     * Suppression d'une ligne dans l'une des tables explicitement autorisées.
     */
    public function delete(string $table, int $id)
    {
        if (! array_key_exists($table, self::TABLES_SUPPRIMABLES)) {
            abort(404);
        }

        $modele = self::TABLES_SUPPRIMABLES[$table];
        $enregistrement = $modele::findOrFail($id);

        // Un Boss ne peut pas supprimer son propre compte par cette voie :
        // il se retirerait tout accès à l'administration.
        if ($modele === User::class && (int) $enregistrement->id === (int) auth()->id()) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte depuis cette page.');
        }

        // Le fichier physique disparaît avec l'enregistrement, sinon le
        // stockage conserve indéfiniment des orphelins.
        if ($modele === File::class && $enregistrement->path) {
            Storage::delete($enregistrement->path);
        }

        $enregistrement->delete();

        return redirect()->back()->with('success', 'Suppression effectuée avec succès');
    }

    public function archiverfile(int $id)
    {
        $file = File::findOrFail($id);
        $file->update(['status' => 'archive']);

        return redirect()->back()->with('success', 'Fichier archivé avec succès');
    }
}
