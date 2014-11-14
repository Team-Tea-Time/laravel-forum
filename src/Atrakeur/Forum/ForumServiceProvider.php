<?php namespace Atrakeur\Forum;

use Illuminate\Support\ServiceProvider;
use Atrakeur\Forum\Commands\InstallCommand;

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
		$this->package('atrakeur/forum');

		if (\Config::get('forum::routes.enable')) {
			$routebase      = \Config::get('forum::routes.base');
			$viewController = \Config::get('forum::integration.viewcontroller');
			$postController = \Config::get('forum::integration.postcontroller');

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
		$this->app['foruminstallcommand'] = $this->app->share(function($app)
        {
            return new InstallCommand;
        });
		$this->commands('foruminstallcommand');
	}

}
