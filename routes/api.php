<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\Api\User\CurrentUserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:api'])->namespace('Api')->group(
    function () {
        //
        Route::apiResource('user', 'User\UserController');
        Route::post('logout', 'User\UserController@logout');
        Route::get('current-user', 'User\CurrentUserController');

        Route::apiResource('file', 'File\FileController');
        Route::apiResource('translate', 'Translate\TranslateController');
        Route::apiResource('language', 'Language\LanguageController');
        Route::apiResource('key', 'Key\KeyController');
        //
    }
);