<?php

namespace App\Providers;
use BeyondCode\LaravelWebSockets\Facades\WebSocketsRouter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use App\Http\Controllers\WS\Service\ClientSocketHandler;
use App\Http\Controllers\WS\Service\AuthenticateDashboard;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        WebSocketsRouter::webSocket('/app/{appKey}', ClientSocketHandler::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Route::post('api/websocket/auth', AuthenticateDashboard::class);
    }
}
