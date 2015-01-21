<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-28 13:57
 *
 */

require_once __DIR__.'/autoload.php';

use Nickfan\AppBox\Foundation\AppBox;
use Nickfan\AppBox\Foundation\BoxSettings;
use Nickfan\AppBox\Config\BoxRepository;
use Nickfan\AppBox\Config\BoxRouteConf;
use Nickfan\AppBox\Instance\BoxRouteInstance;

if(!function_exists('appbox')){
    function appbox(){
        static $app;
        if(empty($app)){
            AppBox::instSettings(BoxSettings::factory(array(
                'path' => AppBox::buildRealPaths(require( __DIR__ . '/paths.php')),
            )));
            AppBox::init(function(){
                $paths = AppBox::getInstSetVar('path');
                $app = AppBox::app();
                $app['usercache'] = AppBox::makeUserCacheInstance();
                $app['conf'] = function ($app,$paths) {
                    return new BoxRepository($paths['conf'],$app['usercache']);
                };
                $app['routeconf'] = function ($app,$paths) {
                    return new BoxRouteConf($paths['routeconf'],$app['usercache']);
                };
                $app['routeinst'] = function ($app) {
                    return BoxRouteInstance::getInstance($app['routeconf']);
                };
                return $app;
            });
            $app = AppBox::box();
        }
        return $app;
    }
}
!isset($app) && $app = appbox();
