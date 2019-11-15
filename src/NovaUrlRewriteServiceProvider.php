<?php

namespace Rjvandoesburg\NovaUrlRewrite;

use Illuminate\Support\ServiceProvider;
use Rjvandoesburg\NovaUrlRewrite\Contracts\UrlRewriteBuilder as UrlRewriteBuilderContract;
use Rjvandoesburg\NovaUrlRewrite\Contracts\UrlRewriteRepository as UrlRewriteRepositoryContract;

class NovaUrlRewriteServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(Providers\RouteServiceProvider::class);

        $this->mergeConfigFrom(
            __DIR__.'/../config/url_rewrite.php',
            'url_rewrite'
        );

        $this->commands([
            Console\RegenerateCommand::class,
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->registerPublishing();
        }

        $model = app(config('url_rewrite.models.url_rewrite'));

        $this->app->singleton(UrlRewriteRepositoryContract::class, function ($app) use ($model) {
            return new UrlRewriteRepository($model);
        });

        $this->app->bind(UrlRewriteBuilderContract::class, function ($app) use ($model) {
            return new UrlRewriteBuilder($this->app->make(UrlRewriteRepositoryContract::class), $model);
        });

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang/', 'nova_url_rewrite');
        $this->loadJsonTranslationsFrom(resource_path('lang/vendor/nova_url_rewrite'));
    }

    /**
     * Register the package's publishable resources.
     */
    protected function registerPublishing(): void
    {
        $this->publishes([
            __DIR__.'/../config/url_rewrite.php' => config_path('url_rewrite.php'),
        ], 'nova-url-rewrite-config');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'nova-url-rewrite-migrations');

        $this->publishes([
            __DIR__.'/../resources/lang/' => resource_path('lang/vendor/nova_url_rewrite'),
        ], 'nova-url-rewrite-translations');
    }
}
