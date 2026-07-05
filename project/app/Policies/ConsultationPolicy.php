<?php

namespace App\Policies;

use App\Models\Consultation;
use App\Models\User;
use App\Data\Value\Account\Role;
use Illuminate\Auth\Access\Response;

class ConsultationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === Role::ADMIN->value || $user->role === Role::DOCTOR->value;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Consultation $consultation): bool
    {
        if ($user->role === Role::ADMIN->value) {
            return true;
        } 

        // Dokter cuma bisa lihat konsultasi yang dihandle
        if ($user->role === Role::DOCTOR->value) {
            return $consultation->onlineSession->doctor === $user->username;
        }

        return false;
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
    public function update(User $user, Consultation $consultation): bool
    {
        return $user->role === Role::ADMIN->value;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Consultation $consultation): bool
    {
        return $user->role === Role::ADMIN->value;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Consultation $consultation): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Consultation $consultation): bool
    {
        //
    }
}
