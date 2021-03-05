<?php

namespace App\Policies;

use App\Models\Language;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LanguagePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Language $model)
    {
        return false;
    }

    public function create(User $user)
    {
        $access = $user->getAccess();
        return $access['root'] ? true : in_array(Language::class, $access['create']);
    }

    public function update(User $user, Language $model)
    {
        $access = $user->getAccess();
        return $access['root'] ? true : in_array(Language::class, $access['update']);
    }

    public function delete(User $user, Language $model)
    {
        $access = $user->getAccess();
        return $access['root'] ? true : in_array(Language::class, $access['destroy']);
    }

    public function restore(User $user, Language $model)
    {
        $access = $user->getAccess();
        return $access['root'] ? true : in_array(Language::class, $access['destroy']);
    }

    public function forceDelete(User $user, Language $model)
    {
        return false;
    }
}
