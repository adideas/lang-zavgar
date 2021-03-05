<?php

namespace App\Policies;

use App\Models\Translate;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TranslatePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Translate $model)
    {
        return false;
    }

    public function create(User $user)
    {
        return false;
    }

    public function update(User $user, Translate $model)
    {
        $access      = $user->getAccess();
        $language_id = '0' . request()->input('language_id');

        return $access['root'] ? true : in_array($language_id, $access['translate']['update']);
    }

    public function delete(User $user, Translate $model)
    {
        return false;
    }

    public function restore(User $user, Translate $model)
    {
        return false;
    }

    public function forceDelete(User $user, Translate $model)
    {
        return false;
    }
}
