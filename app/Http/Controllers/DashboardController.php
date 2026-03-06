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
    

    public function archives()
    {

        return view('dashboard.pages.archives.archives');
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
