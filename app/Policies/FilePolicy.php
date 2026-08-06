<?php

namespace App\Policies;

use App\Models\File;
use App\Models\User;

class FilePolicy
{
    /**
     * Le Boss conserve un accès complet sur tous les fichiers.
     */
    public function before(User $user, string $ability): ?bool
    {
        return $user->isBoss() ? true : null;
    }

    /**
     * Un fichier n'est consultable que par son propriétaire.
     */
    public function view(User $user, File $file): bool
    {
        return $this->possede($user, $file);
    }

    public function update(User $user, File $file): bool
    {
        return $this->possede($user, $file);
    }

    public function delete(User $user, File $file): bool
    {
        return $this->possede($user, $file);
    }

    public function download(User $user, File $file): bool
    {
        return $this->possede($user, $file);
    }

    private function possede(User $user, File $file): bool
    {
        return (int) $file->user_id === (int) $user->id;
    }
}
