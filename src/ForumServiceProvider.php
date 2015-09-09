<?php

namespace Riari\Forum;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Riari\Forum\Models\Category;
use Riari\Forum\Models\Post;
use Riari\Forum\Models\Thread;
use Riari\Forum\Models\Observers\CategoryObserver;
use Riari\Forum\Models\Observers\PostObserver;
use Riari\Forum\Models\Observers\ThreadObserver;

class ForumServiceProvider extends ServiceProvider
{
    /**
     * The namespace for the package controllers.
     *
     * @var string
     */
    protected $namespace;

    /**
     * The policy mappings for the package.
     *
     * @var array
     */
    protected $policies;

    /**
     * The base directory for the package.
     *
     * @var string
     */
    protected $baseDir;

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

        // Create facade alias
        $loader = AliasLoader::getInstance();
        $loader->alias('Forum', 'Riari\Forum\Support\Facades\Forum');
    }

    /**
     * Bootstrap the application events.
     *
     * @param  Router  $router
     * @param  GateContract  $gate
     * @return void
     */
    public function boot(Router $router, GateContract $gate)
    {
        // Set the package base directory
        $this->baseDir = __DIR__.'/../';

        // Register publishable files
        $this->registerPublishables();

        // Load config/views/translations
        $this->loadStaticFiles();

        // Set the package namespace
        $this->namespace = config('forum.integration.controllers.namespace');

        // Load routes (if routing enabled)
        if (config('forum.routing.enabled')) {
            $this->loadRoutes($router);
        }

        // Register model observers
        $this->registerObservers();

        // Set the package policies
        $this->setPolicies();

        // Register policies
        $this->registerPolicies($gate);
    }

    /**
     * Register files published by this package.
     *
     * @return void
     */
    protected function registerPublishables()
    {
        $this->publishes([
            "{$this->baseDir}config/integration.php" => config_path('forum.integration.php'),
            "{$this->baseDir}config/preferences.php" => config_path('forum.preferences.php'),
            "{$this->baseDir}config/routing.php" => config_path('forum.routing.php')
        ], 'config');

        $this->publishes([
            "{$this->baseDir}views/" => base_path('/resources/views/vendor/forum')
        ], 'views');

        $this->publishes([
            "{$this->baseDir}migrations/" => base_path('/database/migrations')
        ], 'migrations');
    }

    /**
     * Load config, views and translations (including application-overridden versions).
     *
     * @return void
     */
    protected function loadStaticFiles()
    {
        // Merge config
        $this->mergeConfigFrom("{$this->baseDir}config/integration.php", 'forum.integration');
        $this->mergeConfigFrom("{$this->baseDir}config/preferences.php", 'forum.preferences');
        $this->mergeConfigFrom("{$this->baseDir}config/routing.php", 'forum.routing');

        // Load views
        $this->loadViewsFrom("{$this->baseDir}views", 'forum');

        // Load translations
        $this->loadTranslationsFrom("{$this->baseDir}translations", 'forum');
    }

    /**
     * Load routes.
     *
     * @param  Router  $router
     * @return void
     */
    protected function loadRoutes(Router $router)
    {
        $dir = $this->baseDir;
        $router->group(['namespace' => $this->namespace], function ($router) use ($dir)
        {
            $root = config('forum.routing.root');
            $parameters = config('forum.routing.parameters');
            $controllers = config('forum.integration.controllers');
            require "{$dir}routes.php";
        });
    }

    /**
     * Register model observers.
     *
     * @return void
     */
    protected function registerObservers()
    {
        Category::observe(new CategoryObserver);
        Thread::observe(new ThreadObserver);
        Post::observe(new PostObserver);
    }

    /**
     * Set the package policies.
     *
     * @return void
     */
    protected function setPolicies()
    {
        $policies = config('forum.integration.policies');
        $this->policies = [
            Category::class => $policies['category'],
            Thread::class   => $policies['thread'],
            Post::class     => $policies['post']
        ];
    }
}
