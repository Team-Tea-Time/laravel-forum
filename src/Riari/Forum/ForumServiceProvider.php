<?php namespace Riari\Forum;

use Illuminate\Support\ServiceProvider;
use Riari\Forum\Commands\InstallCommand;

use Config;

class ForumServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('riari/laravel-forum', 'riari/forum');

		if (Config::get('forum::routes.enable')) {
			$root = Config::get('forum::routes.root');
			$controller = Config::get('forum::integration.controller');

			include __DIR__.'/../../macros.php';
			include __DIR__.'/../../routes.php';
		}
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerCommands();
	}

	/**
	 * Register package artisan commands.
	 *
	 * @return void
	 */
	public function registerCommands()
	{
		$this->app['foruminstallcommand'] = $this->app->share(function()
    {
        return new InstallCommand;
    });

		$this->commands('foruminstallcommand');
	}

}
