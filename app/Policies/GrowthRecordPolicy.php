<?php

namespace App\Policies;

use App\Models\GrowthRecord;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GrowthRecordPolicy
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

    public function view(User $user, GrowthRecord $growthRecord)
    {
        return $user->isManager() || $growthRecord->user_id === $user->id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, GrowthRecord $growthRecord)
    {
        return $user->isManager() || ($growthRecord->user_id === $user->id && $growthRecord->created_at->gt(now()->subHours(24)));
    }

    public function delete(User $user, GrowthRecord $growthRecord)
    {
        return $user->isManager() || $growthRecord->user_id === $user->id;
    }
}