<?php

namespace App\Http\Controllers\WS\Service;

use BeyondCode\LaravelWebSockets\Dashboard\Http\Controllers\AuthenticateDashboard as AuthenticateDashboardExtends;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AuthenticateDashboard extends AuthenticateDashboardExtends
{
    public function __invoke(Request $request)
    {
        $auth = parent::__invoke($request);
        if (isset($auth['auth'])) {
            Cache::forget($request->input('socket_id'));
            Cache::remember(
                $request->input('socket_id'),
                3600,
                function () use ($request) {
                    return JWT::decode(
                        $request->bearerToken(),
                        file_get_contents(storage_path('oauth-public.key')),
                        ['RS256']
                    )->sub;
                }
            );
        }

        return $auth;
    }
}
