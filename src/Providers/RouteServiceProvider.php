<?php

namespace Rjvandoesburg\NovaUrlRewrite\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Rjvandoesburg\NovaUrlRewrite\Http\Controllers\Api\TemplateController;
use Rjvandoesburg\NovaUrlRewrite\Http\Controllers\UrlRewriteController;

class RouteServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::macro('NovaUrlRewrite', function () {
            $nova = ltrim(config('nova.path'), '/');
            $pattern = "^(?!{$nova}|nova-api).*$";

            Route::any('/{request_path?}', UrlRewriteController::class)
                ->where('request_path', $pattern);
        });
    }
}
