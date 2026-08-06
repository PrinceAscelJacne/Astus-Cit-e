<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Le Boss conserve un accès complet sur tous les projets.
     */
    public function before(User $user, string $ability): ?bool
    {
        return $user->isBoss() ? true : null;
    }

    /**
     * Un projet est visible s'il est global (sans département) ou s'il
     * appartient au département de l'utilisateur.
     */
    public function view(User $user, Project $project): bool
    {
        if (is_null($project->department_id)) {
            return true;
        }

        return (int) $project->department_id === (int) $user->department_id;
    }

    /**
     * Seuls les chefs de département créent des projets (le Boss passe par before()).
     */
    public function create(User $user): bool
    {
        return $user->isChefDepartement();
    }

    /**
     * Modifier, archiver ou supprimer reste réservé au créateur du projet.
     * C'est la règle déjà appliquée par la vue dashboard/pages/project/index.
     */
    public function update(User $user, Project $project): bool
    {
        return (int) $project->user_id === (int) $user->id;
    }

    public function delete(User $user, Project $project): bool
    {
        return $this->update($user, $project);
    }

    public function archive(User $user, Project $project): bool
    {
        return $this->update($user, $project);
    }
}
