<?php

namespace App\Http\Controllers\Api\Language;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Language::class);
    }

    public function index()
    {
        return Language::select('id', 'name', 'description')->get();
    }

    public function store(Request $request)
    {
        //
        return Language::create($request->only(['name', 'description']));
    }

    public function update(Request $request, Language $language)
    {
        //
        return $language->update($request->only(['name', 'description']));
    }

    public function destroy(Language $language)
    {
        $language->delete();
    }
}
