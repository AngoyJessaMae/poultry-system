<?php

namespace App\Policies;

use App\Models\MortalityRecord;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MortalityRecordPolicy
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

    public function view(User $user, MortalityRecord $mortalityRecord)
    {
        return $user->isManager() || $mortalityRecord->user_id === $user->id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, MortalityRecord $mortalityRecord)
    {
        return $user->isManager() || ($mortalityRecord->user_id === $user->id && $mortalityRecord->created_at->gt(now()->subHours(24)));
    }

    public function delete(User $user, MortalityRecord $mortalityRecord)
    {
        return $user->isManager();
    }
}