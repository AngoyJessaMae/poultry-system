<?php

namespace App\Policies;

use App\Models\HealthRecord;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class HealthRecordPolicy
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

    public function view(User $user, HealthRecord $healthRecord)
    {
        return $user->isManager() || $healthRecord->user_id === $user->id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, HealthRecord $healthRecord)
    {
        return $user->isManager() || $healthRecord->user_id === $user->id;
    }

    public function delete(User $user, HealthRecord $healthRecord)
    {
        return $user->isManager();
    }
}