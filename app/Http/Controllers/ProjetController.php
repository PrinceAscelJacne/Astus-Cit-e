<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjetController extends Controller
{
    public function index(Request $request)
    {
        $projectsQuery = Project::with('department')
            ->leftJoin('departments', 'projects.department_id', '=', 'departments.id')
            ->select('projects.*', 'departments.name as department_name')
            ->where('projects.status', 'actif')
            ->where(function ($query) {
                $userDepartmentId = auth()->user()->department_id;
                if (!is_null($userDepartmentId)) {
                    $query->where('projects.department_id', $userDepartmentId)
                        ->orWhereNull('projects.department_id');
                }
            });

        // Recherche
        if ($request->filled('search')) {
            $projectsQuery->where('projects.name', 'like', '%' . $request->search . '%')
                          ->orWhere('projects.description', 'like', '%' . htmlspecialchars($request->search). '%');
        }

        // Tri par date de création
        if ($request->filled('sort_by_date')) {
            $projectsQuery->orderBy('projects.created_at', $request->sort_by_date);
        } else {
            $projectsQuery->orderBy('projects.created_at', 'desc');
        }

        $projects = $projectsQuery->get();

        // Regrouper les projets par date de création
        $groupedProjects = $projects->groupBy(function ($project) {
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
            } else {
                return 'Il y a plus d\'un an';
            }
        });

        return view('dashboard.pages.project.index', compact('groupedProjects'));
    }


    public function archives()
    {
        return view('dashboard.pages.project.index');
    }

    public function create()
    {
        $sections = Department::all();
        $user = Auth::user();
        if ($user && $user->department && $user->role_id == 2) {
            $sections = $user->department->name;
        }

        return view('dashboard.pages.project.create',compact('sections'));
    }

    public function store (Request $request)
    {

        $file = null;
        $path = null;
        if ($request->hasFile('fichier'))
        {
            $request->validate([
                'fichier' => 'required|file|mimes:jpg,jpeg,png,pdf,docx,txt,csv,json,md|max:2048',
            ]);
            $file = $request->file('fichier');
            $path = $file->store('projectfiles');
            $file-> $file->getClientOriginalName();
        }

        $department = null;
        if ($request->has('department'))
        {
            $department = $request->department;
        }

        // Création du projet
        Project::create([
            'name' =>  htmlspecialchars($request->nom) ,
            'description' => htmlspecialchars($request->description),
            'department_id' => $department,
            'filename' =>$file,
            'path' => $path,
            'user_id' => Auth::user()->id
        ]);

        return redirect()->back()->with('success', 'Projet créé avec succès.');

    }
    
    public function modify($id)
    {
        $project = Project::findOrFail($id);
        $sections = Department::all();


        return view('dashboard.pages.project.modify', compact('project', 'sections'));
    }



    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $file = null;
        if ($request->hasFile('fichier')) {
            $file = $request->file('fichier');
            $filePath = $file->store('votre-dossier', 'public');
        }
        $department = null;
        if ($request->has('department'))
        {
            $department = $request->department;
        }

        $project->update([
            'name' => $request->nom,
            'description' => $request->description,
            'department_id' => $department,

            'file' => $file,
        ]);
        return redirect()->route('projects')->with('success', 'Projet mis à jour avec succès');
    }

    public function delete($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();
        return redirect()->route('projects')->with('success', 'Projet supprimé avec succès');
    }

    public function archiverproject ($id)
    {
        $projects = Project::findOrFail($id);
        $projects->update([
            'status' => 'archive'
        ]);

        $projects->files()->update([
            'status' => 'archive'
        ]);


        return redirect()->back()->with('success', 'Projet archivé avec succès');
    }

    public function projetarchives()
    {
        $projects = Project::with('department')->leftJoin('departments', 'projects.department_id', '=', 'departments.id')
            ->select('projects.*', 'departments.name as department_name')
            ->where('projects.status', 'archive')
            ->where(function ($query) {
                $userDepartmentId = auth()->user()->department_id;
                if (!is_null($userDepartmentId)) {
                    $query->where('projects.department_id', $userDepartmentId)
                          ->orWhereNull('projects.department_id');
                }
            })
            ->get();
        return view('dashboard.pages.project.archives', compact('projects'));
    }


}
