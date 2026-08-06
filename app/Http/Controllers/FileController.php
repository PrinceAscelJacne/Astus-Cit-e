<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Project;
use App\Models\Department;
use App\Support\GroupeParAnciennete;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    use GroupeParAnciennete;

    /**
     * Taille maximale d'un envoi, en kilo-octets (512 Mo).
     *
     * Une agence d'événementiel dépose des vidéos et des maquettes : la
     * limite précédente de 20 Mo ne suffisait pas. Attention, PHP impose
     * sa propre limite (upload_max_filesize), plus basse par défaut.
     */
    private const TAILLE_MAX_KO = 524288;

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = File::with('project')
            ->where('user_id', $user->id)
            ->where('status', 'actif');

        if ($request->filled('search')) {
            // Paramètre lié : pas d'échappement HTML ici, la valeur sert à
            // comparer en base et Blade échappe déjà à l'affichage.
            $query->where('filename', 'like', '%' . $request->search . '%');
        }

        $sortByDate = $request->sort_by_date;
        if ($sortByDate && in_array($sortByDate, ['asc', 'desc'], true)) {
            $query->orderBy('created_at', $sortByDate);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $groupedFiles = $this->grouperParDate($query->get());
        $projects = $this->projetsVisibles($user);
        $dep = Department::all();

        return view('dashboard.pages.file.index', compact('groupedFiles', 'projects', 'dep'));
    }

    public function create()
    {
        $projects = $this->projetsVisibles(Auth::user());

        return view('dashboard.pages.file.create', compact('projects'));
    }

    public function store(Request $request)
    {
        if ($request->file_content) {
            $donnees = $request->validate([
                'file_content' => 'required|string',
                'project_id' => 'nullable|integer|exists:projects,id',
                'type' => 'required|in:brouillon,officiel',
            ]);

            $this->ecrireDocx($donnees['file_content'], new File(), $donnees);

            return redirect()->back()->with('success', 'Fichier enregistré avec succès.');
        }

        $messages = [
            'fichier.required' => 'Veuillez sélectionner un fichier à déposer.',
            'fichier.file' => 'Le fichier doit être un fichier valide.',
            'fichier.mimes' => 'Ce format n\'est pas accepté.',
            'fichier.max' => 'Le fichier ne peut pas dépasser 512 Mo.',
        ];

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'fichier' => 'required|file|mimes:' . implode(',', File::extensionsAutorisees()) . '|max:' . self::TAILLE_MAX_KO,
            'projet' => 'nullable|integer|exists:projects,id',
            'type' => 'required|in:brouillon,officiel',
            // Le déposant choisit qui voit son fichier ; à défaut, l'équipe
            // du projet, qui est le cas courant du travail à distance.
            'visibilite' => 'nullable|in:' . implode(',', array_keys(File::VISIBILITES)),
            'description' => 'nullable|string|max:255',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Echec de la soumission ! ' . $validator->errors()->first());
        }

        $file = $request->file('fichier');
        $path = $file->store('files');

        File::create([
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            // Conservés pour lister et prévisualiser sans ouvrir le fichier.
            'mime' => $file->getClientMimeType(),
            'taille' => $file->getSize(),
            'description' => $request->description,
            'user_id' => Auth::id(),
            'project_id' => $request->projet,
            'type' => $request->type,
            'status' => 'actif',
            'visibilite' => $request->visibilite ?: 'equipe',
        ]);

        return redirect()->back()->with('success', 'Fichier déposé avec succès.');
    }

    public function modify(int $id)
    {
        $file = File::findOrFail($id);
        $this->authorize('update', $file);

        $projects = $this->projetsVisibles(Auth::user());

        return view('dashboard.pages.file.modify', compact('file', 'projects'));
    }

    /**
     * Renvoie le contenu d'un document .docx converti en HTML, pour l'éditeur.
     */
    public function getFileContent(string $filename)
    {
        $fileModel = File::where('filename', $filename)->firstOrFail();
        $this->authorize('view', $fileModel);

        if (! Storage::exists($fileModel->path)) {
            return response()->json(['error' => 'Fichier non trouvé'], 404);
        }

        $phpWord = \PhpOffice\PhpWord\IOFactory::load(Storage::path($fileModel->path));
        $htmlWriter = new \PhpOffice\PhpWord\Writer\HTML($phpWord);

        ob_start();
        $htmlWriter->save('php://output');
        $htmlContent = ob_get_clean();

        return response()->json(['content' => $htmlContent]);
    }

    public function update(Request $request)
    {
        $donnees = $request->validate([
            'file_id' => 'required|integer|exists:files,id',
            'file_content' => 'required|string',
            'project_id' => 'nullable|integer|exists:projects,id',
            'type' => 'required|in:brouillon,officiel',
        ]);

        $file = File::findOrFail($donnees['file_id']);
        $this->authorize('update', $file);

        $ancienChemin = $file->path;

        $this->ecrireDocx($donnees['file_content'], $file, $donnees);

        // L'ancienne version n'est plus référencée : sans cette suppression,
        // chaque modification laissait un fichier orphelin sur le disque.
        if ($ancienChemin && $ancienChemin !== $file->path) {
            Storage::delete($ancienChemin);
        }

        return redirect()->route('viewfile')->with('success', 'Fichier mis à jour avec succès.');
    }

    public function delete(int $id)
    {
        $file = File::findOrFail($id);
        $this->authorize('delete', $file);

        Storage::delete($file->path);
        $file->delete();

        return redirect()->back()->with('success', 'Fichier supprimé avec succès');
    }

    public function download(int $id)
    {
        $file = File::findOrFail($id);
        $this->authorize('download', $file);

        if (! Storage::exists($file->path)) {
            return redirect()->back()->with('error', 'Fichier introuvable sur le serveur.');
        }

        // La colonne se nomme « filename » ; « name » n'existe pas sur cette
        // table et le téléchargement partait donc sans nom.
        return Storage::download($file->path, $file->filename);
    }

    public function archives()
    {
        $files = File::with('project')
            ->where('user_id', Auth::id())
            ->where('status', 'archive')
            ->get();

        return view('dashboard.pages.file.archives', compact('files'));
    }

    /* Brouillons */

    public function brouillon(Request $request)
    {
        $query = File::where('user_id', Auth::id())->where('type', 'brouillon');

        if ($request->filled('search')) {
            $query->where('filename', 'like', '%' . $request->search . '%');
        }

        $sortByDate = $request->sort_by_date;
        if ($sortByDate && in_array($sortByDate, ['asc', 'desc'], true)) {
            $query->orderBy('created_at', $sortByDate);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $files = $query->get();
        $groupedFiles = $this->grouperParDate($files);

        return view('dashboard.pages.file.brouillon', compact('files', 'groupedFiles'));
    }

    public function deletebrouillon()
    {
        $brouillons = File::where('user_id', Auth::id())->where('type', 'brouillon')->get();

        if ($brouillons->isEmpty()) {
            return redirect()->back()->with('error', 'Aucun fichier à supprimer.');
        }

        foreach ($brouillons as $file) {
            // Le stockage était laissé intact : seules les lignes en base
            // disparaissaient.
            Storage::delete($file->path);
            $file->delete();
        }

        return redirect()->back()->with('success', 'Tous les fichiers ont été supprimés avec succès.');
    }

    /**
     * Génère un .docx à partir du HTML de l'éditeur et met à jour le modèle.
     *
     * @param  array<string, mixed>  $donnees
     */
    private function ecrireDocx(string $htmlContent, File $file, array $donnees): void
    {
        $lines = explode("\n", strip_tags($htmlContent));
        $firstLine = isset($lines[0]) ? Str::slug(trim($lines[0])) : 'file';
        $firstLine = $firstLine !== '' ? $firstLine : 'file';

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        \PhpOffice\PhpWord\Shared\Html::addHtml($section, $htmlContent, false, false);

        $fileName = $firstLine . '_' . now()->format('Ymd_His') . '.docx';
        $tempFilePath = tempnam(sys_get_temp_dir(), 'docx');

        try {
            $phpWord->save($tempFilePath, 'Word2007');
            Storage::put('files/' . $fileName, file_get_contents($tempFilePath));
        } finally {
            // Le fichier temporaire disparaît même si l'écriture échoue.
            if (is_file($tempFilePath)) {
                unlink($tempFilePath);
            }
        }

        $file->filename = $fileName;
        $file->path = 'files/' . $fileName;
        $file->user_id = $file->user_id ?: Auth::id();
        $file->project_id = $donnees['project_id'] ?? null;
        $file->type = $donnees['type'];
        $file->status = 'actif';
        $file->save();
    }

    /**
     * Projets actifs visibles par l'utilisateur : ceux de son département,
     * plus ceux qui n'en ont aucun.
     */
    private function projetsVisibles($user)
    {
        $query = Project::where('status', 'actif');

        if ($user->department_id) {
            $query->where(function ($q) use ($user) {
                $q->where('department_id', $user->department_id)
                  ->orWhereNull('department_id');
            });
        }

        return $query->get();
    }

}
