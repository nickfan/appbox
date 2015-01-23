<?php namespace Nickfan\Appbox\Embed\Laravel;

use Illuminate\Support\ServiceProvider;

class AppboxServiceProvider extends ServiceProvider {

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
		$this->package('nickfan/appbox', 'appbox', __DIR__.'/../../../../');

		$app = $this->app;
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array(
			'boxconf',
			'boxdict',
			'boxrouteconf',
			'boxrouteinst',
			);
	}

}
