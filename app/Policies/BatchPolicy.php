<?php

namespace App\Policies;

use App\Models\Batch;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BatchPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->isManager()) {
            return true;
        }
    }

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Batch $batch)
    {
        return $user->isManager() || $batch->creator->id === $user->id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Batch $batch)
    {
        return $user->isManager() || ($batch->creator->id === $user->id && $batch->created_at->gt(now()->subHours(24)));
    }

    public function delete(User $user, Batch $batch)
    {
        return $user->isManager();
    }
}