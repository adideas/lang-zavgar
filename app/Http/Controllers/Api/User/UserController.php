<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Language;
use App\User;
use App\UserAccess;
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
        return User::create(
            [
                'name'     => $request->input('name', $request->input('email')),
                'email'    => $request->input('email'),
                'password' => bcrypt($request->input('password')),
            ]
        );
    }

    public function show(User $user)
    {
        //
        return $user;
    }

    public function update(Request $request, User $user)
    {
        //
        $data = [];
        $data['name'] = $request->input('name');
        $data['email'] = $request->input('email');

        if($request->has('password') && $request->input('password')) {
            if(Hash::check($request->input('confirm_password'), $user->password)) {
                $data['password'] = bcrypt($request->input('password'));
            }
        }

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

    public function listAccess() {
        $accessed = [];
        Language::each(
            function (Language $language) use (&$accessed) {
                array_push($accessed, [
                    'value' =>  Language::class .':' . $language->id . ':' . 0,
                    'label' => "[$language->name] $language->description - Просмотр"
                ]);
                array_push($accessed, [
                    'value' =>  Language::class .':' . $language->id . ':' . 1,
                    'label' => "[$language->name] $language->description - Редактирование"
                ]);
            }
        );

        array_push($accessed, [
            'value' =>  File::class .':' . 0,
            'label' => "Создание файлов"
        ]);

        array_push($accessed, [
            'value' =>  File::class .':' . 1,
            'label' => "Редактирование файлов"
        ]);

        array_push($accessed, [
            'value' =>  File::class .':' . 2,
            'label' => "Удаление файлов"
        ]);

        array_push($accessed, [
            'value' =>  User::class .':' . 0,
            'label' => "Создание пользователей"
        ]);

        array_push($accessed, [
            'value' =>  User::class .':' . 1,
            'label' => "Редактирование пользователей"
        ]);

        array_push($accessed, [
            'value' =>  User::class .':' . 2,
            'label' => "Удаление пользователей"
        ]);

        array_push($accessed, [
            'value' =>  Language::class .':' . 0 . ':' . 0,
            'label' => "Добавление языка"
        ]);
        array_push($accessed, [
            'value' =>  Language::class .':' . 0 . ':' . 1,
            'label' => "Удаление языка"
        ]);

        return $accessed;
    }
}
