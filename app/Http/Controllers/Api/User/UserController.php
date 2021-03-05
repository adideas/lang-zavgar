<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Access;
use App\Models\File;
use App\Models\Language;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class);
    }

    public function index(Request $request)
    {
        if ($request->input('to')) {
            return User::paginate($request->input('to', 10));
        }

        return User::all();
    }

    public function store(Request $request)
    {
        $user = User::create(
            [
                'name'     => $request->input('name', $request->input('email')),
                'email'    => $request->input('email'),
                'password' => bcrypt($request->input('password')),
            ]
        );

        $user->access()->sync($request->input('access_id', []));

        return $user;
    }

    public function show(User $user)
    {
        //
        $user->access_id = $user->access->pluck('id');
        return $user;
    }

    public function update(Request $request, User $user)
    {
        //
        $data          = [];
        $data['name']  = $request->input('name');
        $data['email'] = $request->input('email');

        if ($request->has('password') && $request->input('password')) {
            if (Hash::check($request->input('confirm_password'), $user->password)) {
                $data['password'] = bcrypt($request->input('password'));
            }
        }

        $diff = array_diff($user->access()->pluck('access_id')->toArray(), $request->input('access_id', []));

        if(count($diff)) {
            cache()->forget('getAccess_user_'.$user->id);
        }

        $user->access()->sync($request->input('access_id', []));

        return $user->update($data);
    }

    public function destroy(User $user)
    {
        //
        $user->delete();
    }

    public function logout()
    {
        auth()->user()->token()->revoke();

        //
        return abort(response()->json([], 200));
    }

    public function listAccess()
    {
        return Access::orderByDesc('id')->get();
    }
}
