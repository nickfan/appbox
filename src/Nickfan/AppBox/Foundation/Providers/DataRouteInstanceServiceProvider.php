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

use Nickfan\AppBox\Instance\DataRouteInstance;
use Nickfan\AppBox\Support\ServiceProvider;

class DataRouteInstanceServiceProvider extends ServiceProvider {

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
    public function register() {
        $this->app->bindShared(
            'datarouteinstance',
            function ($app) {
                return DataRouteInstance::getInstance($app['datarouteconf']);
            }
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return array('datarouteinstance');
    }

}
