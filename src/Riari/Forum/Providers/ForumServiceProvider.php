<?php namespace Riari\Forum\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class ForumServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'Riari\Forum\Http\Controllers';

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(__DIR__.'/../../../config/integration.php', 'forum.integration');
        $this->mergeConfigFrom(__DIR__.'/../../../config/permissions.php', 'forum.permissions');
        $this->mergeConfigFrom(__DIR__.'/../../../config/preferences.php', 'forum.preferences');
        $this->mergeConfigFrom(__DIR__.'/../../../config/routing.php', 'forum.routing');
    }

    /**
     * Bootstrap the application events.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function boot(Router $router)
    {
        // Register publishable files (config, views and migrations)
        $this->publishes([
            __DIR__.'/../../../config/integration.php' => config_path('forum.integration.php'),
            __DIR__.'/../../../config/permissions.php' => config_path('forum.permissions.php'),
            __DIR__.'/../../../config/preferences.php' => config_path('forum.preferences.php'),
            __DIR__.'/../../../config/routing.php' => config_path('forum.routing.php')
        ], 'config');

        $this->publishes([
            __DIR__.'/../../../views/' => base_path('/resources/views/vendor/forum')
        ], 'views');

        $this->publishes([
            __DIR__.'/../../../migrations/' => base_path('/database/migrations')
        ], 'migrations');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../../../views', 'forum');

        // Load translations
        $this->loadTranslationsFrom(__DIR__.'/../../../translations', 'forum');

        // Load routes (if routing enabled)
        if (config('forum.routing.enabled')) {
            $router->group(['namespace' => $this->namespace, 'middleware' => 'forum.permissions'], function ($router)
            {
                $root = config('forum.routing.root');
                $controllers = config('forum.integration.controllers');
                require __DIR__.'/../../../routes.php';
            });
        }

        // Register middleware
        $router->middleware('forum.auth.basic', 'Riari\Forum\Http\Middleware\BasicAuth');
        $router->middleware('forum.permissions', 'Riari\Forum\Http\Middleware\CheckPermissions');

        // Load helpers
        require __DIR__.'/../../../helpers.php';
    }
}
