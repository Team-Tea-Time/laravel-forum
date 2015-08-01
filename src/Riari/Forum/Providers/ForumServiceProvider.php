<?php namespace Riari\Forum\Providers;

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
     * Package root path.
     *
     * @var string
     */
    protected $root = __DIR__.'/../../../';

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Bind the forum facade
        $this->app->bind('forum', function()
        {
            return new \Riari\Forum\Forum;
        });

        // Create facade aliases
        $loader = AliasLoader::getInstance();
        $loader->alias('Forum', 'Riari\Forum\Support\Facades\Forum');
        $loader->alias('ForumRoute', 'Riari\Forum\Support\Facades\Route');
    }

    /**
     * Bootstrap the application events.
     *
     * @param  Router  $router
     * @return void
     */
    public function boot(Router $router)
    {
        // Register publishable files (config, views and migrations)
        $this->publishes([
            "{$this->root}config/integration.php" => config_path('forum.integration.php'),
            "{$this->root}config/permissions.php" => config_path('forum.permissions.php'),
            "{$this->root}config/preferences.php" => config_path('forum.preferences.php'),
            "{$this->root}config/routing.php" => config_path('forum.routing.php')
        ], 'config');

        $this->publishes([
            "{$this->root}views/" => base_path('/resources/views/vendor/forum')
        ], 'views');

        $this->publishes([
            "{$this->root}migrations/" => base_path('/database/migrations')
        ], 'migrations');

        // Merge config
        $this->mergeConfigFrom("{$this->root}config/integration.php", 'forum.integration');
        $this->mergeConfigFrom("{$this->root}config/permissions.php", 'forum.permissions');
        $this->mergeConfigFrom("{$this->root}config/preferences.php", 'forum.preferences');
        $this->mergeConfigFrom("{$this->root}config/routing.php", 'forum.routing');

        // Load views
        $this->loadViewsFrom("{$this->root}views", 'forum');

        // Load translations
        $this->loadTranslationsFrom("{$this->root}translations", 'forum');

        // Set the namespace
        $this->namespace = config('forum.integration.controllers.namespace');

        // Load routes (if routing enabled)
        $middleware = (config('forum.permissions.enabled')) ? ['middleware' => 'forum.permissions'] : [];
        if (config('forum.routing.enabled')) {
            $router->group(['namespace' => $this->namespace] + $middleware, function ($router)
            {
                $root = config('forum.routing.root');
                $parameters = config('forum.routing.parameters');
                $controllers = config('forum.integration.controllers');
                require "{$this->root}routes.php";
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
