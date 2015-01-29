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
		$this->registerBoxUsercache();
		$this->registerBoxDict();
		$this->registerBoxConf();
		$this->registerBoxRouteConf();
		$this->registerBoxRouteInst();
	}

	protected function getPathBase(){
		static $path_base;
		if(!$path_base){
			$path_base = $this->app['config']->get('appbox::path.base');
		}
		return $path_base;
	}
	public function registerBoxUsercache() {
		if($this->app['config']->get('appbox::usercache.singleton')==true){
			$this->app->singleton('boxusercache',function($app)
			{
				$usercacheSettings = $this->app['config']->get('appbox::usercache');
				switch($usercacheSettings['driver']){
					case 'auto':
						return new \Nickfan\AppBox\Common\Usercache\AutoBoxUsercache($usercacheSettings['options']);
						break;
					case 'apc':
						return new \Nickfan\AppBox\Common\Usercache\ApcBoxUsercache($usercacheSettings['options']);
						break;
					case 'yac':
						return new \Nickfan\AppBox\Common\Usercache\YacBoxUsercache($usercacheSettings['options']);
						break;
					case 'redis':
						return new \Nickfan\AppBox\Common\Usercache\RedisBoxUsercache($usercacheSettings['options']);
						break;
					case 'null':
					case 'none':
					default:
						return new \Nickfan\AppBox\Common\Usercache\NullBoxUsercache($usercacheSettings['options']);
				}
			});
		}else{
			$this->app['boxusercache'] = $this->app->share(function($app)
			{
				$usercacheSettings = $this->app['config']->get('appbox::usercache');
				switch($usercacheSettings['driver']){
					case 'auto':
						return new \Nickfan\AppBox\Common\Usercache\AutoBoxUsercache($usercacheSettings['options']);
						break;
					case 'apc':
						return new \Nickfan\AppBox\Common\Usercache\ApcBoxUsercache($usercacheSettings['options']);
						break;
					case 'yac':
						return new \Nickfan\AppBox\Common\Usercache\YacBoxUsercache($usercacheSettings['options']);
						break;
					case 'redis':
						return new \Nickfan\AppBox\Common\Usercache\RedisBoxUsercache($usercacheSettings['options']);
						break;
					case 'null':
					case 'none':
					default:
						return new \Nickfan\AppBox\Common\Usercache\NullBoxUsercache($usercacheSettings['options']);
				}
			});
		}
		// Shortcut so developers don't need to add an Alias in app/config/app.php
		$this->app->booting(function() {
			$loader = \Illuminate\Foundation\AliasLoader::getInstance();
			$loader->alias('BoxUsercache', 'Nickfan\AppBox\Embed\Laravel\Facades\BoxUsercache');
		});
	}

	public function registerBoxDict() {
		if($this->app['config']->get('appbox::dict.singleton')==true){
			$this->app->singleton('boxdict',function($app)
			{
				return new \Nickfan\AppBox\Config\BoxDictionary(array(),$this->app['config']->get('appbox::dict.options'));
			});
		}else{
			$this->app['boxdict'] = $this->app->share(function($app)
			{
				return new \Nickfan\AppBox\Config\BoxDictionary(array(),$this->app['config']->get('appbox::dict.options'));
			});
		}
		// Shortcut so developers don't need to add an Alias in app/config/app.php
		$this->app->booting(function() {
			$loader = \Illuminate\Foundation\AliasLoader::getInstance();
			$loader->alias('BoxDict', 'Nickfan\AppBox\Embed\Laravel\Facades\BoxDict');
		});
	}

	public function registerBoxConf() {
		$this->app->singleton('boxconf',function($app)
		{
			$path_base = $this->getPathBase();
			return new \Nickfan\AppBox\Config\BoxRepository($path_base.'/conf',$app->make('boxusercache'));
		});
		// Shortcut so developers don't need to add an Alias in app/config/app.php
		$this->app->booting(function() {
			$loader = \Illuminate\Foundation\AliasLoader::getInstance();
			$loader->alias('BoxConf', 'Nickfan\AppBox\Embed\Laravel\Facades\BoxConf');
		});
	}
	public function registerBoxRouteConf() {
		$this->app->singleton('boxrouteconf',function($app)
		{
			$path_base = $this->getPathBase();
			return new \Nickfan\AppBox\Config\BoxRouteConf($path_base.'/etc',$app->make('boxusercache'));
		});
		// Shortcut so developers don't need to add an Alias in app/config/app.php
		$this->app->booting(function() {
			$loader = \Illuminate\Foundation\AliasLoader::getInstance();
			$loader->alias('BoxRouteConf', 'Nickfan\AppBox\Embed\Laravel\Facades\BoxRouteConf');
		});
	}

	public function registerBoxRouteInst() {
		$this->app->singleton('boxrouteinst',function($app)
		{
			$path_base = $this->getPathBase();
			return \Nickfan\AppBox\Instance\BoxRouteInstance::getInstance($app->make('boxrouteconf'));
		});
		// Shortcut so developers don't need to add an Alias in app/config/app.php
		$this->app->booting(function() {
			$loader = \Illuminate\Foundation\AliasLoader::getInstance();
			$loader->alias('BoxRouteInst', 'Nickfan\AppBox\Embed\Laravel\Facades\BoxRouteInst');
		});
	}



	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array(
			'boxusercache',
			'boxdict',
			'boxconf',
			'boxrouteconf',
			'boxrouteinst',
			);
	}

}
