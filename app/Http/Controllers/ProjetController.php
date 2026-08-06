<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProjetController extends Controller
{
    /**
     * @var array<int, string>
     */
    private const EXTENSIONS_AUTORISEES = [
        'jpg', 'jpeg', 'png', 'pdf', 'docx', 'txt', 'csv', 'json', 'md',
    ];

    private const TAILLE_MAX_KO = 20480;

    public function index(Request $request)
    {
        $projectsQuery = $this->projetsVisibles('actif');

        if ($request->filled('search')) {
            $recherche = '%' . $request->search . '%';

            // Les deux conditions de recherche sont groupées : au niveau
            // supérieur, le orWhere annulait le filtrage par département et
            // laissait remonter les projets des autres services.
            $projectsQuery->where(function ($q) use ($recherche) {
                $q->where('projects.name', 'like', $recherche)
                  ->orWhere('projects.description', 'like', $recherche);
            });
        }

        $sortByDate = $request->sort_by_date;
        if ($sortByDate && in_array($sortByDate, ['asc', 'desc'], true)) {
            $projectsQuery->orderBy('projects.created_at', $sortByDate);
        } else {
            $projectsQuery->orderBy('projects.created_at', 'desc');
        }

        $groupedProjects = $this->grouperParDate($projectsQuery->get());

        return view('dashboard.pages.project.index', compact('groupedProjects'));
    }

    public function create()
    {
        $this->authorize('create', Project::class);

        $user = Auth::user();
        $sections = Department::all();

        if ($user->department && $user->isChefDepartement()) {
            $sections = $user->department->name;
        }

        return view('dashboard.pages.project.create', compact('sections'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Project::class);

        $donnees = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'department' => 'nullable|integer|exists:departments,id',
            'fichier' => 'nullable|file|mimes:' . implode(',', self::EXTENSIONS_AUTORISEES) . '|max:' . self::TAILLE_MAX_KO,
        ], [
            'nom.required' => 'Veuillez renseigner le nom du projet.',
            'fichier.mimes' => 'Le fichier doit être de type :values.',
            'fichier.max' => 'Le fichier ne peut pas dépasser 20 Mo.',
        ]);

        $user = Auth::user();

        // Un chef de département ne crée que dans son propre département,
        // quelle que soit la valeur postée.
        $departmentId = $user->isChefDepartement()
            ? $user->department_id
            : ($donnees['department'] ?? null);

        $filename = null;
        $path = null;

        if ($request->hasFile('fichier')) {
            $fichier = $request->file('fichier');
            $path = $fichier->store('projectfiles');
            // On stocke le nom d'origine, pas l'objet UploadedFile lui-même.
            $filename = $fichier->getClientOriginalName();
        }

        Project::create([
            'name' => $donnees['nom'],
            // La colonne description est NOT NULL : on retombe sur une chaîne vide.
            'description' => $donnees['description'] ?? '',
            'department_id' => $departmentId,
            'filename' => $filename,
            'path' => $path,
            'status' => 'actif',
            'user_id' => $user->id,
        ]);

        return redirect()->back()->with('success', 'Projet créé avec succès.');
    }

    public function modify(int $id)
    {
        $project = Project::findOrFail($id);
        $this->authorize('update', $project);

        $sections = Department::all();

        return view('dashboard.pages.project.modify', compact('project', 'sections'));
    }

    public function update(Request $request, int $id)
    {
        $project = Project::findOrFail($id);
        $this->authorize('update', $project);

        $donnees = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'department' => 'nullable|integer|exists:departments,id',
            'fichier' => 'nullable|file|mimes:' . implode(',', self::EXTENSIONS_AUTORISEES) . '|max:' . self::TAILLE_MAX_KO,
        ], [
            'nom.required' => 'Veuillez renseigner le nom du projet.',
            'fichier.mimes' => 'Le fichier doit être de type :values.',
            'fichier.max' => 'Le fichier ne peut pas dépasser 20 Mo.',
        ]);

        $user = Auth::user();

        $attributs = [
            'name' => $donnees['nom'],
            // La colonne description est NOT NULL : on retombe sur une chaîne vide.
            'description' => $donnees['description'] ?? '',
            'department_id' => $user->isChefDepartement()
                ? $user->department_id
                : ($donnees['department'] ?? null),
        ];

        if ($request->hasFile('fichier')) {
            $ancienChemin = $project->path;
            $fichier = $request->file('fichier');

            // Le chemin calculé n'était jamais enregistré : l'ancien code
            // écrivait dans une colonne « file » qui n'existe pas, et le
            // fichier téléversé était donc perdu.
            $attributs['path'] = $fichier->store('projectfiles');
            $attributs['filename'] = $fichier->getClientOriginalName();

            if ($ancienChemin) {
                Storage::delete($ancienChemin);
            }
        }

        $project->update($attributs);

        return redirect()->route('viewproject')->with('success', 'Projet mis à jour avec succès');
    }

    public function delete(int $id)
    {
        $project = Project::findOrFail($id);
        $this->authorize('delete', $project);

        if ($project->path) {
            Storage::delete($project->path);
        }

        $project->delete();

        return redirect()->route('viewproject')->with('success', 'Projet supprimé avec succès');
    }

    public function archiverproject(int $id)
    {
        $project = Project::findOrFail($id);
        $this->authorize('archive', $project);

        $project->update(['status' => 'archive']);
        $project->files()->update(['status' => 'archive']);

        return redirect()->back()->with('success', 'Projet archivé avec succès');
    }

    public function projetarchives()
    {
        $projects = $this->projetsVisibles('archive')->get();

        return view('dashboard.pages.project.archives', compact('projects'));
    }

    /**
     * Projets d'un statut donné, restreints au département de l'utilisateur
     * (plus ceux qui n'ont pas de département). Le Boss voit tout.
     */
    private function projetsVisibles(string $status)
    {
        $query = Project::with('department')
            ->leftJoin('departments', 'projects.department_id', '=', 'departments.id')
            ->select('projects.*', 'departments.name as department_name')
            ->where('projects.status', $status);

        $user = Auth::user();

        if (! $user->isBoss() && ! is_null($user->department_id)) {
            $query->where(function ($q) use ($user) {
                $q->where('projects.department_id', $user->department_id)
                  ->orWhereNull('projects.department_id');
            });
        }

        return $query;
    }

    private function grouperParDate($projects)
    {
        return $projects->groupBy(function ($project) {
            $now = \Carbon\Carbon::now();
            $created = \Carbon\Carbon::parse($project->created_at);

            if ($created->isToday()) {
                return 'Aujourd\'hui';
            } elseif ($created->isYesterday()) {
                return 'Hier';
            } elseif ($created->diffInDays($now) <= 7) {
                return 'Cette semaine';
            } elseif ($created->diffInDays($now) <= 30) {
                return 'Ce mois-ci';
            } elseif ($created->diffInMonths($now) <= 12) {
                return 'Cette année';
            }

            return 'Il y a plus d\'un an';
        });
    }
}
