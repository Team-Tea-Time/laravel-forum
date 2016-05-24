<?php namespace Riari\Forum;

use Illuminate\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Riari\Forum\Http\Middleware\APIAuth;
use Riari\Forum\Models\Post;
use Riari\Forum\Models\Thread;
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
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
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
        $this->baseDir = __DIR__.'/../';

        $this->setPublishables();
        $this->loadStaticFiles();

        $this->namespace = 'Riari\Forum\Http\Controllers';

        $this->observeModels();

        $this->registerPolicies($gate);

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
            "{$this->baseDir}migrations/" => base_path('database/migrations')
        ], 'migrations');

        $this->publishes([
            "{$this->baseDir}translations/" => base_path('resources/lang/vendor/forum'),
        ], 'translations');
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
        $forumPolicy = config('forum.integration.policies.forum');
        foreach (get_class_methods(new $forumPolicy()) as $method) {
            $gate->define($method, "{$forumPolicy}@{$method}");
        }

        foreach (config('forum.integration.policies.model') as $model => $policy) {
            $gate->policy($model, $policy);
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
        $router->group([
            'namespace' => $this->namespace,
            'as' => config('forum.routing.as'),
            'prefix' => config('forum.routing.root')
        ], function ($r) use ($dir) {
            require "{$dir}routes.php";
        });
    }

    /**
     * Register middleware.
     *
     * @return void
     */
    public function registerMiddleware(Router $router)
    {
        $router->middleware('forum.api.auth', APIAuth::class);
    }
}
