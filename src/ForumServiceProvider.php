<?php

namespace TeamTeaTime\Forum;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use TeamTeaTime\Forum\Config\FrontendStack;
use TeamTeaTime\Forum\Console\Commands\InstallPreset;
use TeamTeaTime\Forum\Console\Commands\Seed;
use TeamTeaTime\Forum\Console\Commands\SyncStats;
use TeamTeaTime\Forum\Frontends\Blade;
use TeamTeaTime\Forum\Frontends\FrontendInterface;
use TeamTeaTime\Forum\Frontends\Livewire;
use TeamTeaTime\Forum\Http\Middleware\ResolveApiParameters;

class ForumServiceProvider extends ServiceProvider
{
    private const CONFIG_FILES = [
        'api',
        'features',
        'frontend',
        'general',
        'integration',
    ];

    private ?FrontendInterface $frontend = null;

    public function __construct($app)
    {
        parent::__construct($app);

        foreach (self::CONFIG_FILES as $key) {
            $this->mergeConfigFrom(__DIR__."/../config/{$key}.php", "forum.{$key}");
        }

        $config = config('forum.features.frontend');

        switch ($config) {
            case FrontendStack::NONE:
                break;
            case FrontendStack::BLADE:
                $this->frontend = new Blade;
                break;
            case FrontendStack::LIVEWIRE:
                if (!class_exists(\Livewire\Livewire::class)) {
                    Log::error('The forum frontend stack is set to Livewire, but Livewire is not installed. Please install it: composer require livewire/livewire');
                    break;
                }

                $this->frontend = new Livewire;
                break;
        }
    }

    public function register()
    {
        if (isset($this->frontend)) {
            $this->callAfterResolving(BladeCompiler::class, fn () => $this->frontend->register());
        }
    }

    public function boot(Router $router, GateContract $gate)
    {
        $this->publishConfig();
        $this->publishMigrations();
        $this->publishTranslations();

        if (config('forum.features.api')) {
            $this->enableApi($router);
        }

        if (isset($this->frontend)) {
            $this->frontend->configureRouter($router)
                ->group(fn () => $this->loadRoutesFrom($this->frontend->getRoutesPath()));

            $viewsPath = $this->frontend->getViewsPath();
            if ($viewsPath !== null) {
                $this->loadViewsFrom($viewsPath, 'forum');

                View::composer('forum.master', function ($view) {
                    if (Auth::check()) {
                        $nameAttribute = config('forum.integration.user_name');
                        $view->username = Auth::user()->{$nameAttribute};
                    }
                });

                $loader = AliasLoader::getInstance();
                $loader->alias('Forum', config('forum.frontend.utility_class'));
            }
        }

        $this->loadTranslationsFrom(__DIR__.'/../translations', 'forum');

        $this->registerPolicies($gate);

        // Make sure Carbon's locale is set to the application locale
        Carbon::setLocale(config('app.locale'));

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallPreset::class,
                Seed::class,
                SyncStats::class,
            ]);
        }
    }

    private function publishConfig(): void
    {
        $configPathMap = [];
        foreach (self::CONFIG_FILES as $key) {
            $configPathMap[__DIR__."/../config/{$key}.php"] = config_path("forum/{$key}.php");
        }

        $this->publishes($configPathMap, 'config');
    }

    private function publishMigrations(): void
    {
        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'migrations');
    }

    private function publishTranslations(): void
    {
        $this->publishes([
            __DIR__.'/../translations/' => function_exists('lang_path') ? lang_path('vendor/forum') : resource_path('lang/vendor/forum'),
        ], 'translations');
    }

    private function enableApi(Router $router): void
    {
        $config = config('forum.api.router');
        $config['middleware'][] = ResolveApiParameters::class;

        $router
            ->prefix($config['prefix'])
            ->name($config['as'])
            ->namespace($config['namespace'])
            ->middleware($config['middleware'])
            ->group(function () {
                $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
            });
    }

    private function registerPolicies(GateContract $gate): void
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
