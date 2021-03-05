<?php

namespace App\Policies;

use App\Models\File;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FilePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        $access = $user->getAccess();
        return $access['root'] ? true : in_array(File::class, $access['show']);
    }

    public function view(User $user)
    {
        $access = $user->getAccess();
        return $access['root'] ? true : in_array(File::class, $access['show']);
    }

    public function create(User $user)
    {
        $access = $user->getAccess();
        return $access['root'] ? true : in_array(File::class, $access['create']);
    }

    public function update(User $user)
    {
        $access = $user->getAccess();
        return $access['root'] ? true : in_array(File::class, $access['update']);
    }

    public function delete(User $user)
    {
        $access = $user->getAccess();
        return $access['root'] ? true : in_array(File::class, $access['destroy']);
    }

    public function restore(User $user, File $model)
    {
        $access = $user->getAccess();
        return $access['root'] ? true : in_array(File::class, $access['destroy']);
    }

    public function forceDelete(User $user, File $model)
    {
        return false;
    }
}
