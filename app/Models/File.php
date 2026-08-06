<?php

namespace App\Models;

use App\Support\ConsigneLesActivites;
use App\Support\Droits;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory, ConsigneLesActivites;

    /** Attribut servant de libellé dans le journal d'activité. */
    protected $champLibelle = 'filename';

    /**
     * Qui voit le fichier.
     *
     * Le déposant choisit à l'envoi ; la direction peut imposer une autre
     * valeur, et l'on retient alors qui l'a décidé.
     */
    public const VISIBILITES = [
        'equipe' => 'L\'équipe du projet',
        'entreprise' => 'Toute l\'entreprise',
        'direction' => 'La direction seulement',
        'prive' => 'Moi seul',
    ];

    /**
     * Familles de fichiers acceptées.
     *
     * Astuscité produit des visuels, des vidéos et des maquettes : la liste
     * d'origine (12 extensions bureautiques) ne couvrait pas son métier.
     *
     * @var array<string, array<int, string>>
     */
    public const FAMILLES = [
        'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'tiff', 'tif', 'heic', 'svg'],
        'video' => ['mp4', 'mov', 'avi', 'mkv', 'webm', 'm4v', 'mpeg', 'mpg'],
        'audio' => ['mp3', 'wav', 'aac', 'm4a', 'ogg', 'flac'],
        'maquette' => ['psd', 'ai', 'eps', 'indd', 'xd', 'sketch', 'fig', 'afdesign', 'afphoto', 'cdr'],
        'document' => ['pdf', 'doc', 'docx', 'odt', 'rtf', 'txt', 'md', 'xls', 'xlsx', 'ods', 'csv', 'ppt', 'pptx', 'odp'],
        'archive' => ['zip', 'rar', '7z', 'tar', 'gz'],
        'donnees' => ['json', 'xml'],
    ];

    protected $fillable = [
        'filename', 'path', 'mime', 'taille', 'description', 'version',
        'remplace_id', 'type', 'status', 'visibilite', 'visibilite_imposee_par',
        'project_id', 'user_id',
    ];

    protected $casts = ['taille' => 'integer', 'version' => 'integer'];

    /* Relations */

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }

    public function remplace()
    {
        return $this->belongsTo(File::class, 'remplace_id');
    }

    /* Extensions */

    /**
     * Toutes les extensions acceptées, à plat.
     *
     * @return array<int, string>
     */
    public static function extensionsAutorisees(): array
    {
        return array_merge(...array_values(self::FAMILLES));
    }

    public function extension(): string
    {
        return strtolower(pathinfo($this->filename, PATHINFO_EXTENSION));
    }

    /**
     * Famille du fichier : image, video, maquette… Sert à choisir l'icône et
     * à savoir si une prévisualisation est possible.
     */
    public function famille(): string
    {
        $extension = $this->extension();

        foreach (self::FAMILLES as $famille => $extensions) {
            if (in_array($extension, $extensions, true)) {
                return $famille;
            }
        }

        return 'document';
    }

    public function tailleLisible(): string
    {
        $octets = (int) $this->taille;

        if ($octets <= 0) {
            return '—';
        }

        foreach ([['Go', 1073741824], ['Mo', 1048576], ['Ko', 1024]] as [$unite, $seuil]) {
            if ($octets >= $seuil) {
                return round($octets / $seuil, 1) . ' ' . $unite;
            }
        }

        return $octets . ' o';
    }

    /* Visibilité */

    public function visibiliteLisible(): string
    {
        return self::VISIBILITES[$this->visibilite] ?? 'L\'équipe du projet';
    }

    /**
     * Cette personne peut-elle voir ce fichier ?
     *
     * Règle unique, appliquée partout : les listes, le téléchargement et la
     * lecture du contenu s'appuient dessus, pour qu'un fichier restreint ne
     * puisse pas remonter par un chemin détourné.
     */
    public function visiblePar(User $utilisateur): bool
    {
        // Le déposant voit toujours ce qu'il a déposé.
        if ((int) $this->user_id === (int) $utilisateur->id) {
            return true;
        }

        // La direction voit tout : c'est elle qui arbitre.
        if ($utilisateur->isBoss() || $utilisateur->peut(Droits::FICHIERS_GERER_TOUS)) {
            return true;
        }

        return match ($this->visibilite) {
            'prive' => false,
            'direction' => false,
            'entreprise' => true,
            default => $this->project && $this->project->estMembre($utilisateur),
        };
    }

    /**
     * Restreint une requête aux fichiers que la personne a le droit de voir.
     */
    public function scopeVisiblesPar(Builder $query, User $utilisateur): Builder
    {
        if ($utilisateur->isBoss() || $utilisateur->peut(Droits::FICHIERS_GERER_TOUS)) {
            return $query;
        }

        $projetsDeLaPersonne = $utilisateur->projets()->pluck('projects.id');

        return $query->where(function (Builder $q) use ($utilisateur, $projetsDeLaPersonne) {
            $q->where('user_id', $utilisateur->id)
              ->orWhere('visibilite', 'entreprise')
              ->orWhere(function (Builder $q2) use ($projetsDeLaPersonne) {
                  $q2->where('visibilite', 'equipe')
                     ->whereIn('project_id', $projetsDeLaPersonne);
              });
        });
    }

    /**
     * Seule la direction impose une visibilité à la place du déposant.
     */
    public function imposerVisibilite(string $visibilite, User $decideur): void
    {
        $this->forceFill([
            'visibilite' => $visibilite,
            'visibilite_imposee_par' => $decideur->id,
        ])->save();
    }
}
