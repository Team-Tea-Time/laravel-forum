<?php

namespace Riari\Forum;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Riari\Forum\Models\Category;
use Riari\Forum\Models\Post;
use Riari\Forum\Models\Thread;
use Riari\Forum\Models\Observers\CategoryObserver;
use Riari\Forum\Models\Observers\PostObserver;
use Riari\Forum\Models\Observers\ThreadObserver;

class ForumServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Bind the forum facades
        $this->app->bind('forum', function()
        {
            return new \Riari\Forum\Forum;
        });
        $this->app->bind('forumroute', function()
        {
            return new \Riari\Forum\Routing\Route;
        });

        // Create facade aliases
        $loader = AliasLoader::getInstance();
        $loader->alias('Forum', 'Riari\Forum\Support\Facades\Forum');
        $loader->alias('ForumRoute', 'Riari\Forum\Support\Facades\ForumRoute');
    }

    /**
     * Bootstrap the application events.
     *
     * @param  Router  $router
     * @return void
     */
    public function boot(Router $router)
    {
        $dir = __DIR__.'/../';

        // Register publishable files (config, views and migrations)
        $this->publishes([
            "{$dir}config/integration.php" => config_path('forum.integration.php'),
            "{$dir}config/permissions.php" => config_path('forum.permissions.php'),
            "{$dir}config/preferences.php" => config_path('forum.preferences.php'),
            "{$dir}config/routing.php" => config_path('forum.routing.php')
        ], 'config');

        $this->publishes([
            "{$dir}views/" => base_path('/resources/views/vendor/forum')
        ], 'views');

        $this->publishes([
            "{$dir}migrations/" => base_path('/database/migrations')
        ], 'migrations');

        // Merge config
        $this->mergeConfigFrom("{$dir}config/integration.php", 'forum.integration');
        $this->mergeConfigFrom("{$dir}config/permissions.php", 'forum.permissions');
        $this->mergeConfigFrom("{$dir}config/preferences.php", 'forum.preferences');
        $this->mergeConfigFrom("{$dir}config/routing.php", 'forum.routing');

        // Load views
        $this->loadViewsFrom("{$dir}views", 'forum');

        // Load translations
        $this->loadTranslationsFrom("{$dir}translations", 'forum');

        // Set the namespace
        $this->namespace = config('forum.integration.controllers.namespace');

        // Load routes (if routing enabled)
        $middleware = (config('forum.permissions.enabled')) ? ['middleware' => 'forum.permissions'] : [];
        if (config('forum.routing.enabled')) {
            $router->group(['namespace' => $this->namespace] + $middleware, function ($router) use ($dir)
            {
                $root = config('forum.routing.root');
                $parameters = config('forum.routing.parameters');
                $controllers = config('forum.integration.controllers');
                require "{$dir}routes.php";
            });
        }

        // Register middleware
        $router->middleware('forum.auth.basic', 'Riari\Forum\Http\Middleware\BasicAuth');
        if (config('forum.permissions.enabled')) {
            $router->middleware('forum.permissions', 'Riari\Forum\Http\Middleware\CheckPermissions');
        }

        // Register model observers
        Category::observe(new CategoryObserver);
        Thread::observe(new ThreadObserver);
        Post::observe(new PostObserver);
    }
}
