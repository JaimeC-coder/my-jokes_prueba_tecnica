<?php

namespace App\Providers;

use App\Http\Middleware\AuthenticateWithToken;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Route::aliasMiddleware('auth.token', AuthenticateWithToken::class);
    }
}
