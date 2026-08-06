<?php

namespace App\Policies;

use App\Models\File;
use App\Models\User;
use App\Support\Droits;

class FilePolicy
{
    /**
     * La direction arbitre : elle voit et gère tout.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isBoss() || $user->peut(Droits::FICHIERS_GERER_TOUS)) {
            return true;
        }

        return null;
    }

    /**
     * La lecture suit la visibilité choisie par le déposant, ou imposée par
     * la direction. La règle vit sur le modèle pour que les listes, le
     * téléchargement et l'ouverture du contenu s'appuient tous dessus.
     */
    public function view(User $user, File $file): bool
    {
        return $file->visiblePar($user);
    }

    public function download(User $user, File $file): bool
    {
        return $file->visiblePar($user);
    }

    /**
     * Modifier ou supprimer reste réservé au déposant : un collègue qui voit
     * un fichier ne doit pas pouvoir l'écraser.
     */
    public function update(User $user, File $file): bool
    {
        return $this->possede($user, $file);
    }

    public function delete(User $user, File $file): bool
    {
        return $this->possede($user, $file);
    }

    /**
     * Seule la direction impose une visibilité à la place du déposant : c'est
     * elle qui tranche sur ce qui doit rester confidentiel.
     */
    public function imposerVisibilite(User $user, File $file): bool
    {
        return false;
    }

    private function possede(User $user, File $file): bool
    {
        return (int) $file->user_id === (int) $user->id;
    }
}
