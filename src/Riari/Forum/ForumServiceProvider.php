<?php namespace Riari\Forum;

use Event;
use Riari\Forum\Events\ThreadWasViewed;
use Illuminate\Support\ServiceProvider;

class ForumServiceProvider extends ServiceProvider {

    /**
    * Register the service provider.
    *
    * @return void
    */
    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(__DIR__.'/../../config/integration.php', 'forum.integration');
        $this->mergeConfigFrom(__DIR__.'/../../config/permissions.php', 'forum.permissions');
        $this->mergeConfigFrom(__DIR__.'/../../config/preferences.php', 'forum.preferences');
        $this->mergeConfigFrom(__DIR__.'/../../config/routing.php', 'forum.routing');
    }

    /**
    * Bootstrap the application events.
    *
    * @return void
    */
    public function boot()
    {
        // Publish controller, config, views and migrations
        $this->publishes([
            __DIR__.'/Controllers/ForumController.php' => base_path('app/Http/Controllers/ForumController.php')
        ], 'controller');

        $this->publishes([
            __DIR__.'/../../config/integration.php' => config_path('forum.integration.php'),
            __DIR__.'/../../config/permissions.php' => config_path('forum.permissions.php'),
            __DIR__.'/../../config/preferences.php' => config_path('forum.preferences.php'),
            __DIR__.'/../../config/routing.php' => config_path('forum.routing.php')
        ], 'config');

        $this->publishes([
            __DIR__.'/../../views/' => base_path('/resources/views/vendor/forum')
        ], 'views');

        $this->publishes([
            __DIR__.'/../../translations/' => base_path('/resources/lang/vendor/forum')
        ], 'lang');

        $this->publishes([
            __DIR__.'/../../migrations/' => database_path('migrations')
        ], 'migrations');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../../views', 'forum');

        // Load translations
        $this->loadTranslationsFrom(__DIR__.'/../../translations', 'forum');

        // Load routes, if enabled
        if (config('forum.routing.enabled')) {
            $root = config('forum.routing.root');
            $controller = config('forum.integration.controller');

            include __DIR__.'/../../routes.php';
        }

        // Subscribe event Handlers
        Event::subscribe('Riari\Forum\Handlers\Events\IncrementThreadViewCount');
    }

}
