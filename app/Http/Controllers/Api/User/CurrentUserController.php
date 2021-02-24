<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\User\CurrentUserResource;
use App\User;
use Illuminate\Http\Request;

class CurrentUserController extends Controller
{
    public function __invoke()
    {
        return new CurrentUserResource(auth()->user());
    }
}
