<?php

namespace App\Support;

use App\Models\Activite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Consigne automatiquement les actions faites sur un modèle.
 *
 * Branché sur les événements Eloquent plutôt que sur des appels dispersés
 * dans les contrôleurs : une écriture ajoutée plus tard, où que ce soit, est
 * tracée sans qu'on ait à y penser. C'est ce qui rend le journal fiable pour
 * trancher en cas de litige.
 *
 * Le modèle qui l'utilise peut définir :
 *   - $libelleActivite  : nom de l'attribut servant de libellé lisible
 *   - $ignoreActivite   : attributs à ne jamais consigner (mots de passe…)
 *   - projetAssocie()   : identifiant du projet de rattachement
 */
trait ConsigneLesActivites
{
    public static function bootConsigneLesActivites(): void
    {
        static::created(function (Model $modele) {
            $modele->consigner('cree');
        });

        static::updated(function (Model $modele) {
            $champs = $modele->champsModifies();

            // Une mise à jour qui ne touche aucun champ suivi n'a pas à
            // encombrer le journal.
            if (empty($champs)) {
                return;
            }

            // Le passage en « archive » est une action à part entière : la
            // consigner comme une simple modification la rendrait invisible.
            $action = ($champs['status']['apres'] ?? null) === 'archive'
                ? 'archive'
                : 'modifie';

            $modele->consigner($action, $champs);
        });

        // « deleting » et non « deleted » : l'enregistrement doit exister
        // encore au moment où l'entrée est écrite, sinon la clé étrangère du
        // journal vers ce même enregistrement est refusée. Le lien se dénoue
        // ensuite tout seul (nullOnDelete), mais le libellé et l'identifiant
        // restent consignés.
        static::deleting(function (Model $modele) {
            $modele->consigner('supprime');
        });
    }

    /**
     * Écrit une entrée dans le journal.
     *
     * @param  array<string, mixed>|null  $details
     */
    public function consigner(string $action, ?array $details = null): void
    {
        $utilisateur = Auth::user();

        Activite::create([
            'user_id' => $utilisateur?->id,
            // Le nom est recopié : le journal doit rester lisible même après
            // suppression du compte.
            'auteur_nom' => $utilisateur
                ? trim(($utilisateur->surname ?? '') . ' ' . $utilisateur->name)
                : null,
            'action' => $action,
            'sujet_type' => static::class,
            'sujet_id' => $this->getKey(),
            'sujet_libelle' => $this->libelleActivite(),
            'project_id' => $this->projetAssocie(),
            'details' => $details,
        ]);
    }

    /**
     * Champs réellement modifiés, hors champs ignorés, avec avant/après.
     *
     * @return array<string, array{avant: mixed, apres: mixed}>
     */
    public function champsModifies(): array
    {
        $ignores = array_merge(
            ['updated_at', 'created_at', 'password', 'remember_token'],
            property_exists($this, 'ignoreActivite') ? $this->ignoreActivite : []
        );

        $resultat = [];

        foreach ($this->getChanges() as $champ => $apres) {
            if (in_array($champ, $ignores, true)) {
                continue;
            }

            $resultat[$champ] = [
                'avant' => $this->getOriginal($champ),
                'apres' => $apres,
            ];
        }

        return $resultat;
    }

    /**
     * Libellé lisible du sujet, pour que le journal se lise en clair.
     */
    public function libelleActivite(): ?string
    {
        $attribut = property_exists($this, 'champLibelle')
            ? $this->champLibelle
            : 'name';

        return $this->{$attribut} ?? null;
    }

    /**
     * Projet de rattachement, s'il y en a un.
     */
    public function projetAssocie(): ?int
    {
        if ($this instanceof \App\Models\Project) {
            return $this->getKey();
        }

        return $this->project_id ?? null;
    }

    public function activites()
    {
        return $this->morphMany(Activite::class, 'sujet', 'sujet_type', 'sujet_id');
    }
}
