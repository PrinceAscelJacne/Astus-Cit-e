<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\File;
use App\Models\Project;
use App\Models\Department;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FileController extends Controller
{

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = File::with('project')->where('user_id', $user->id)->where('status', 'actif');

        // Recherche par nom de fichier
        if ($request->has('search')) {
            $query->where('filename', 'like', '%' . htmlspecialchars($request->search) . '%');
        }

        // Tri par date de création
        $sortByDate = $request->sort_by_date;
        if ($sortByDate && in_array($sortByDate, ['asc', 'desc'])) {
            $query->orderBy('created_at', $sortByDate);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Filtrage par projet
        $projectId = $request->project_id;
        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        $files = $query->get();

        // Regroupement des fichiers par date
        $groupedFiles = $files->groupBy(function ($file) {
            $now = \Carbon\Carbon::now();
            $created = \Carbon\Carbon::parse($file->created_at);

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

        // Récupération de tous les projets pour la liste déroulante
        if ($user->department_id) {
            $projects = Project::where('status', 'actif')
                    ->where(function($query) use ($user) {
                        $query->where('department_id', $user->department_id)
                            ->orWhereNull('department_id');
                    })
                    ->get();
        } else {
            $projects = Project::where('status', 'actif')->get();
        }

        $dep = Department::all();

        return view('dashboard.pages.file.index', compact('groupedFiles', 'projects', 'dep'));
    }



    public function create()
    {
        $user = auth()->user();
        if ($user->department_id) {
            $projects = Project::where('status', 'actif')
                    ->where(function($query) use ($user) {
                        $query->where('department_id', $user->department_id)
                              ->orWhereNull('department_id');
                    })
                    ->get();
        } else
        {
            $projects = Project::where('status', 'actif')->get();
        }
        return view('dashboard.pages.file.create',compact('projects'));
    }

    public function store(Request $request)
    {
        if($request->file_content)
        {

            $request->validate([
                'file_content' => 'required|string',
                'project_id' => 'nullable|integer',
            ]);

            // Extraire la première ligne du contenu
            $htmlContent = $request->input('file_content');
            $lines = explode("\n", strip_tags($htmlContent)); // Retirer les balises HTML et diviser en lignes
            $firstLine = isset($lines[0]) ? Str::slug(trim($lines[0])) : 'file'; // Utiliser une version slugifiée de la première ligne
            // Créer un nouvel objet PHPWord
            $phpWord = new PhpWord();
            $section = $phpWord->addSection();
            // Convertir le contenu HTML en objets PHPWord
            \PhpOffice\PhpWord\Shared\Html::addHtml($section, $htmlContent, false, false);

            $fileName = $firstLine . '_' . now()->format('Ymd_His') . '.docx';
            $tempFilePath = tempnam(sys_get_temp_dir(), 'docx');
            $phpWord->save($tempFilePath, 'Word2007');

            $fileContent = file_get_contents($tempFilePath);
            $storedPath = 'files/' . $fileName;
            Storage::put('files/' . $fileName, $fileContent);

            unlink($tempFilePath);

            // Créer une nouvelle entrée dans la base de données
            $fileModel = new File();
            $fileModel->filename = $fileName;
            $fileModel->path = $storedPath;
            $fileModel->user_id = Auth::user()->id;
            $fileModel->project_id = $request->project_id;
            $fileModel->type = $request->type;
            $fileModel->status = 'actif';
            $fileModel->save();

            return redirect()->back()->with('success', 'Fichier enregistré avec succès.');
        }
        else

        {
            $messages = [
                'fichier.required' => 'Veuillez sélectionner un fichier à télécharger.',
                'fichier.file' => 'Le fichier doit être un fichier valide.',
                'fichier.mimes' => 'Le fichier doit être de type :values.',
                'fichier.max' => 'Le fichier ne peut pas dépasser 200 Mo.',
            ];
            $validator = Validator::make($request->all(),[
                'fichier' => 'required|file|mimes:jpg,jpeg,png,mp4,mp3,zip, pdf,docx,txt,csv,json,md|max:204800',

            ],$messages);

            if($validator->fails())
            {
                return redirect()->back()->with('error', ('Echec de la soumission ! '.$validator->errors()->first().''));
            }

            $file = $request->file('fichier');

            $path = $file->store('files');

            $fileModel = new File();
            $fileModel->filename = $file->getClientOriginalName();
            $fileModel->path = $path;
            $fileModel->user_id = Auth::user()->id;
            $fileModel->project_id = $request->projet;
            $fileModel->type = $request->type;
            $fileModel->status = 'actif';

            $fileModel->save();

            return redirect()->back()->with('success', 'Fichier enregistré avec succès.');
        }

    }


    public function modify($id)
    {
        $file = File::find($id);
        $user = auth()->user();
        if ($user->department_id) {
            $projects = Project::where('status', 'actif')
                    ->where(function($query) use ($user) {
                        $query->where('department_id', $user->department_id)
                            ->orWhereNull('department_id');
                    })
                    ->get();
        } else {
            $projects = Project::where('status', 'actif')->get();
        }
        return view('dashboard.pages.file.modify', compact('file','projects'));
    }

    public function getFileContent($file)
    {
        $fileModel = File::where('filename', $file)->firstOrFail();

        if (Storage::exists($fileModel->path)) {
            $filePath = storage_path('app/' . $fileModel->path);
            // Lire le fichier DOCX
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
            // Configurer l'objet pour écrire en HTML
            $htmlWriter = new \PhpOffice\PhpWord\Writer\HTML($phpWord);
            // Capturer le contenu HTML dans une variable
            ob_start();
            $htmlWriter->save("php://output");
            $htmlContent = ob_get_clean();

            return response()->json(['content' => $htmlContent]);
        } else {
            return response()->json(['error' => 'Fichier non trouvé'], 404);
        }
    }


    public function update(Request $request)
    {
        // Valider les données du formulaire
        $request->validate([
            'file_content' => 'required|string',
            'project_id' => 'nullable|integer',
        ]);
        // Trouver le fichier à mettre à jour
        $file = File::find($request->input('file_id'));
        if (!$file) {
            return redirect()->back()->with('error', 'Fichier non trouvé.');
        }

        // Extraire le contenu et le nom du fichier
        $htmlContent = $request->input('file_content');
        $lines = explode("\n", strip_tags($htmlContent));
        $firstLine = isset($lines[0]) ? Str::slug(trim($lines[0])) : 'file';

        // Créer un nouvel objet PHPWord
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Convertir le contenu HTML en objets PHPWord
        \PhpOffice\PhpWord\Shared\Html::addHtml($section, $htmlContent, false, false);

        // Générer le fichier .docx et le sauvegarder en mémoire
        $fileName = $firstLine . '_' . now()->format('Ymd_His') . '.docx';
        $tempFilePath = tempnam(sys_get_temp_dir(), 'docx');
        $phpWord->save($tempFilePath, 'Word2007');

        // Lire le contenu du fichier temporaire et stocker dans le répertoire files
        $fileContent = file_get_contents($tempFilePath);
        $storedPath = 'files/' . $fileName;
        Storage::put($storedPath, $fileContent);

        // Supprimer le fichier temporaire
        unlink($tempFilePath);

        // Mettre à jour le modèle de fichier
        $file->filename = $fileName;
        $file->path = $storedPath;
        $file->project_id = $request->project_id;
        $file->type = $request->type;
        $file->status = 'actif';
        $file->save();

        return redirect()->route('viewfile')->with('success', 'Fichier mis à jour avec succès.');
    }


    public function delete($id)
    {
        $file = File::findOrFail($id);
        Storage::delete($file->path);
        $file->delete();
        return redirect()->back()->with('success', 'Fichier supprimé avec succès');
    }

    public function download($id)
    {
        $file = File::findOrFail($id);
        return Storage::download($file->path, $file->name) ;
    }

    public function archives()
    {
        $user = auth()->user();
        $files = File::with('project')->where('user_id', $user->id )->where('files.status', 'archive')->get();
        return view('dashboard.pages.file.archives', compact('files'));
    }

    /* Brouillon files */

    public function brouillon(Request $request)
    {
        $user = auth()->user();
        $query = File::where('user_id', $user->id)->where('type', 'brouillon');

        // Recherche par nom de fichier
        if ($request->has('search')) {
            $query->where('filename', 'like', '%' . htmlspecialchars($request->search) . '%');
        }

        // Tri par date de création
        $sortByDate = $request->sort_by_date;
        if ($sortByDate && in_array($sortByDate, ['asc', 'desc'])) {
            $query->orderBy('created_at', $sortByDate);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $files = $query->get();
        // Regroupement des fichiers par date
        $groupedFiles = $files->groupBy(function ($file) {
            $now = \Carbon\Carbon::now();
            $created = \Carbon\Carbon::parse($file->created_at);

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
            } elseif ($created->diffInMonths($now) <= 2) {
                return 'Le mois dernier';
            } elseif ($created->diffInMonths($now) <= 12) {
                return 'Il y\'a plus de deux mois';
            } else {
                return 'Il y a plus d\'un an';
            }
        });
        return view('dashboard.pages.file.brouillon', compact('files','groupedFiles'));
        }

    public function deletebrouillon()
    {
        $user= auth()->user();
        $allfiles = File::where('user_id', $user->id)->where('type', 'brouillon')->get();
        if($allfiles->count() != 00)
        {
            foreach ($allfiles as $file) {
                $file->delete();
            }
            return redirect()->back()->with('success', 'Tous les fichiers ont été supprimés avec succès.');
        } else {
            return redirect()->back()->with('error', 'Aucun fichier à supprimer.');

        }
    }
}


