<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Role;
use App\Models\User;
use App\Models\Project;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function redirect()
    {
        if(Auth::user())
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

        $user = auth()->user();
        $filesQuery = File::with('project')->where('user_id', $user->id)->where('status', 'actif');
        $files = $filesQuery->get();
        $projects = $projectsQuery->get();


        return view('dashboard.pages.dashboard', compact('projects','files'));

        }
    }

    public function index()
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

        $user = auth()->user();
        $filesQuery = File::with('project')->where('user_id', $user->id)->where('status', 'actif');
        $files = $filesQuery->get();
        $projects = $projectsQuery->get();


        return view('dashboard.pages.dashboard', compact('projects','files'));
    }
    

    /**
     * Les projets rangés une fois terminés.
     *
     * Cette page ne recevait aucune donnée : un projet archivé disparaissait
     * de la liste des projets sans réapparaître nulle part. On doit pouvoir le
     * retrouver pour le reprendre ou le présenter à un client.
     */
    public function archives()
    {
        $utilisateur = auth()->user();

        $projets = Project::with('department', 'createur')
            ->withCount('files')
            ->where('status', 'archive')
            ->when(
                ! $utilisateur->isBoss() && ! $utilisateur->peut(\App\Support\Droits::PROJETS_GERER_TOUS),
                // Sans droit global, on ne voit que les projets de son
                // département ou ceux dont on faisait partie.
                fn ($q) => $q->where(function ($q2) use ($utilisateur) {
                    $q2->where('department_id', $utilisateur->department_id)
                       ->orWhereNull('department_id')
                       ->orWhereHas('membres', fn ($q3) => $q3->where('users.id', $utilisateur->id));
                })
            )
            ->latest('updated_at')
            ->paginate(12);

        return view('dashboard.pages.archives.archives', compact('projets'));
    }
    public function nouveautes(){
        return view('dashboard.pages.nouveautes');
    }
    public function aide(){
        return view('dashboard.pages.aide');
    }
    public function apropos(){
        return view('dashboard.pages.apropos');
    }


}
