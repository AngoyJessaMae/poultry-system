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
        return $user->isManager();
    }

    public function view(User $user, Sale $sale)
    {
        return $user->isManager();
    }

    public function create(User $user)
    {
        return $user->isManager();
    }

    public function update(User $user, Sale $sale): bool
    {
        return $user->isManager();
    }

    public function delete(User $user, Sale $sale): bool
    {
        return false;
    }

    public function cancel(User $user, Sale $sale): bool
    {
        return $user->isManager();
    }

}
