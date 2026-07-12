<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;
use App\Data\Value\Account\Role;
use Illuminate\Auth\Access\Response;

class ArticlePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if ($user->role === Role::ADMIN->value) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Article $article): bool
    {
        return true;
    }

    public function viewBackend(User $user): bool
    {
        return $user->role === Role::ADMIN->value || $user->role === Role::DOCTOR->value;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === Role::ADMIN->value;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Article $article): bool
    {
        if ($user->role === Role::ADMIN->value) {
            return true;
        }

        // only can updated own article
        if ($user->role === Role::DOCTOR->value) {
            return $user->username === $article->creator;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Article $article): bool
    {
        if ($user->role === Role::ADMIN->value) {
            return true;
        }

        if ($user->role === Role::DOCTOR->value) {
            return $user->username === $article->creator;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Article $article): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Article $article): bool
    {
        //
    }
}
