<?php

namespace Riari\Forum;

use Blade;
use Illuminate\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Riari\Forum\Events\UserViewingThread;
use Riari\Forum\Listeners\MarkThreadAsRead;
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
     * The base directory for the package.
     *
     * @var string
     */
    protected $baseDir;

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UserViewingThread::class => [
            MarkThreadAsRead::class,
        ],
    ];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerFacades();
    }

    /**
     * Bootstrap the application events.
     *
     * @param  Router  $router
     * @param  GateContract  $gate
     * @param  DispatcherContract  $events
     * @return void
     */
    public function boot(Router $router, GateContract $gate, DispatcherContract $events)
    {
        $this->baseDir = __DIR__.'/../';

        $this->setPublishables();
        $this->loadStaticFiles();

        $this->namespace = config('forum.integration.controllers.namespace');

        $this->observeModels();

        $this->registerPolicies($gate);

        $this->registerListeners($events);

        if (config('forum.routing.enabled')) {
            $this->registerMiddleware($router);
            $this->loadRoutes($router);
        }
    }

    /**
     * Define files published by this package.
     *
     * @return void
     */
    protected function setPublishables()
    {
        $this->publishes([
            "{$this->baseDir}config/api.php" => config_path('forum.api.php'),
            "{$this->baseDir}config/integration.php" => config_path('forum.integration.php'),
            "{$this->baseDir}config/preferences.php" => config_path('forum.preferences.php'),
            "{$this->baseDir}config/routing.php" => config_path('forum.routing.php'),
            "{$this->baseDir}config/validation.php" => config_path('forum.validation.php')
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
        foreach (['api', 'integration', 'preferences', 'routing', 'validation'] as $name) {
            $this->mergeConfigFrom("{$this->baseDir}config/{$name}.php", "forum.{$name}");
        }

        // Load views
        $this->loadViewsFrom("{$this->baseDir}views", 'forum');

        // Load translations
        $this->loadTranslationsFrom("{$this->baseDir}translations", 'forum');
    }

    /**
     * Initialise model observers.
     *
     * @return void
     */
    protected function observeModels()
    {
        Category::observe(new CategoryObserver);
        Thread::observe(new ThreadObserver);
        Post::observe(new PostObserver);
    }

    /**
     * Register the package policies.
     *
     * @param  GateContract  $gate
     * @return void
     */
    public function registerPolicies(GateContract $gate)
    {
        foreach (config('forum.integration.policies') as $key => $value) {
            $gate->policy($key, $value);
        }

        foreach (get_class_methods(new \Riari\Forum\Policies\ForumPolicy) as $method) {
            $gate->define($method, "Riari\Forum\Policies\ForumPolicy@{$method}");
        }
    }

    /**
     * Register the package listeners.
     *
     * @param  DispatcherContract  $events
     * @return void
     */
    public function registerListeners(DispatcherContract $events)
    {
        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                $events->listen($event, $listener);
            }
        }
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
        $router->group(['namespace' => $this->namespace, 'as' => 'forum.', 'prefix' => config('forum.routing.root')], function ($r) use ($dir)
        {
            $controllers = config('forum.integration.controllers');
            require "{$dir}routes.php";
        });
    }

    /**
     * Register the package facades.
     *
     * @return void
     */
    public function registerFacades()
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
     * Register middleware.
     *
     * @return void
     */
    public function registerMiddleware(Router $router)
    {
        $router->middleware('forum.api.auth', 'Riari\Forum\Http\Middleware\APIAuth');
    }
}
