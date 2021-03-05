<?php

namespace App\Http\Controllers\Api\Git;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GitController extends Controller
{
    // сливаем 2 ветки без проблем без всяких там конфликтов
    //
    // git checkout master
    // && git merge -s recursive -Xtheirs develop
    // && git push
    // && git checkout develop

    public function index()
    {

    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
