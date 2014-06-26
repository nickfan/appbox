<?php
/**
 * Description
 *
 * @project appbox
 * @package 
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-19 11:32
 *
 */



namespace Nickfan\AppBox\Foundation\Providers;

use Nickfan\AppBox\Support\ServiceProvider;
use Nickfan\AppBox\Common\Usercache\ApcUsercache;
use Nickfan\AppBox\Config\DataRouteConf;
class DataRouteConfServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('datarouteconf', function($app)
            {
                return new DataRouteConf(new ApcUsercache,$app['path.storage'].'/etc/local');
            });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('datarouteconf');
    }

}