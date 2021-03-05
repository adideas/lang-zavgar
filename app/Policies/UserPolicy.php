<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        $access = $user->getAccess();
        return $access['root'] ? true : in_array(User::class, $access['show']);
    }

    public function view(User $user, User $model)
    {
        $access = $user->getAccess();
        return $access['root'] ? true : in_array(User::class, $access['show']);
    }

    public function create(User $user)
    {
        $access = $user->getAccess();
        return $access['root'] ? true : in_array(User::class, $access['create']);
    }

    public function update(User $user, User $model)
    {
        $access = $user->getAccess();
        return $access['root'] ? true : in_array(User::class, $access['update']);
    }

    public function delete(User $user, User $model)
    {
        $access = $user->getAccess();
        return $access['root'] ? true : in_array(User::class, $access['destroy']);
    }

    public function restore(User $user, User $model)
    {
        $access = $user->getAccess();
        return $access['root'] ? true : in_array(User::class, $access['destroy']);
    }

    public function forceDelete(User $user, User $model)
    {
        return false;
    }
}
