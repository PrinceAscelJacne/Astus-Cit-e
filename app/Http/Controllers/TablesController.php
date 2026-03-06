<?php

namespace App\Http\Controllers;



use App\Models\File;
use App\Models\Role;
use App\Models\User;
use App\Models\Project;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TablesController extends Controller
{
    public function utilisateurs()
    {
        $users = User::with('role','department')->get();

        return view('dashboard.pages.tables.user',compact('users'));
    }

    public function roles()
    {
        $roles = Role::all();

        return view('dashboard.pages.tables.role',compact('roles'));
    }

    public function departements()
    {
        $deps = Department::all();

        return view('dashboard.pages.tables.dep',compact('deps'));
    }

    public function createdep(Request $request)
    {

        $deps = Department::all();
        $val = false;

        foreach ($deps as $deps) {
            if (trim($deps->name) == trim($request->nom)) {
                $val = true;
                break;
            }

        }

        if ($val)
        {
           return redirect()->back()->with('error', 'Ce departement existe déja');
        }
        else
        {
            $dep = new Department();
            $dep->name = $request->nom;
            $dep->save();
            return redirect()->back()->with('success', 'Département a été crée avec succès');
        }

    }

    public function files(Request $request)
    {
        $query = File::with('user', 'project')->where('type','!=','brouillon');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('department')) {
            $query->whereHas('project', function($q) use ($request) {
                $q->where('department_id', $request->department);
            });
        }

        if ($request->filled('project')) {
            $query->where('project_id', $request->project);
        }

        $files = $query->get();
        $dep = Department::all();
        $projects = Project::all();

        return view('dashboard.pages.tables.file', compact('files', 'dep', 'projects'));
    }


    public function projects(request $request)
    {
        $projects = Project::with('department')->get();
        if($request->has('search'))
        {
            $projects = Project::with('department')->where('name', 'like', '%'.$request->search.'%')->orWhere('description','like','%'.$request->search.'%')->get();
        }
        return view('dashboard.pages.tables.project',compact('projects'));
    }

    public function delete($table, $id)
    {
        DB::table($table)->where('id', $id)->delete();

        return redirect()->back()->with('success', 'suppression effectuée avec succès');
    }
    public function archiverfile( $id)
    {
        $file= File::findOrFail($id);
        $file->update([
            'status' => 'archive'
        ]);

        return redirect()->back()->with('success', 'fichier archivé avec succès');
    }
}
