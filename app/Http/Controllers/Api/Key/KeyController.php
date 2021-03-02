<?php

namespace App\Http\Controllers\Api\Key;

use App\Http\Controllers\Controller;
use App\Models\Key;
use App\Models\KeyAndChild;
use Illuminate\Http\Request;

class KeyController extends Controller
{

    public function index(Request $request)
    {
        return Key::get();
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
