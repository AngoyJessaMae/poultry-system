<?php

namespace App\Policies;

use App\Models\FeedingLog;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FeedingLogPolicy
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

    public function view(User $user, FeedingLog $feedingLog)
    {
        return $user->isManager() || $feedingLog->user_id === $user->id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, FeedingLog $feedingLog)
    {
        return $user->isManager() || $user->isWorker();
    }

    public function delete(User $user, FeedingLog $feedingLog)
    {
        return $user->isManager() || $user->isWorker();
    }
}