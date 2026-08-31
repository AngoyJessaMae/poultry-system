<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Station;
use App\Models\User;

class StationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Both managers and workers can view stations
        return $user->isManager() || $user->isWorker();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Station $station): bool
    {
        // Both managers and workers can view individual stations
        return $user->isManager() || $user->isWorker();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Both managers and workers can create stations
        return $user->isManager() || $user->isWorker();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Station $station): bool
    {
        // Both managers and workers can update stations
        return $user->isManager() || $user->isWorker();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Station $station): bool
    {
        // Only managers can delete stations
        return $user->isManager();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Station $station): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Station $station): bool
    {
        return false;
    }
}