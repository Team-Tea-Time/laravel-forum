<?php namespace Riari\Forum;

use Carbon\Carbon;
use Illuminate\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Riari\Forum\Http\ViewComposers\MasterComposer;
use Riari\Forum\Models\Post;
use Riari\Forum\Models\Thread;
use Riari\Forum\Models\Observers\PostObserver;
use Riari\Forum\Models\Observers\ThreadObserver;

class ForumServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @param  Router  $router
     * @param  GateContract  $gate
     * @return void
     */
    public function boot(Router $router, GateContract $gate)
    {
        $this->publishes([
            __DIR__.'/../config/api.php' => config_path('forum.api.php'),
            __DIR__.'/../config/frontend.php' => config_path('forum.frontend.php'),
            __DIR__.'/../config/general.php' => config_path('forum.general.php'),
            __DIR__.'/../config/integration.php' => config_path('forum.integration.php')
        ], 'config');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations')
        ], 'migrations');

        $this->publishes([
            __DIR__.'/../translations/' => resource_path('lang/vendor/forum'),
        ], 'translations');

        foreach (['api', 'frontend', 'general', 'integration'] as $name) {
            $this->mergeConfigFrom(__DIR__."/../config/{$name}.php", "forum.{$name}");
        }

        if (config('forum.api.enabled')) {
            $router->group(config('forum.api.router'), function ($r) {
                require __DIR__.'/../routes/api.php';
            });
        }

        if (config('forum.frontend.enabled')) {
            $this->publishes([
                __DIR__.'/../views/' => resource_path('views/vendor/forum')
            ], 'views');

            $router->group(config('forum.frontend.router'), function ($r) {
                require __DIR__.'/../routes/frontend.php';
            });

            $this->loadViewsFrom(__DIR__.'/../views', 'forum');
        }

        $this->loadTranslationsFrom(__DIR__.'/../translations', 'forum');

        $this->registerPolicies($gate);

        // Make sure Carbon's locale is set to the application locale
        Carbon::setLocale(config('app.locale'));

        Thread::observe(new ThreadObserver);
        Post::observe(new PostObserver);

        $loader = AliasLoader::getInstance();
        $loader->alias('Forum', config('forum.frontend.utility_class'));

        View::composer('forum::master', function ($view) {
            if (auth()->check()) {
                $nameAttribute = config('forum.integration.user_name');
                $view->username = auth()->user()->{$nameAttribute};
            }
        });
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
}
