<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalePolicy
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

    public function view(User $user, Sale $sale)
    {
        return $user->isManager() || $sale->user_id === $user->id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Sale $sale)
    {
        return $user->isManager() || $user->isWorker();
    }

    public function delete(User $user, Sale $sale)
    {
        return $user->isManager() || $user->isWorker();
    }
}