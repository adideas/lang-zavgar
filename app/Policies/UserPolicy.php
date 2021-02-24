<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, User $model)
    {
        return true;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, User $model)
    {
        return true;
    }

    public function delete(User $user, User $model)
    {
        return true;
    }

    public function restore(User $user, User $model)
    {
        return true;
    }

    public function forceDelete(User $user, User $model)
    {
        return true;
    }
}
