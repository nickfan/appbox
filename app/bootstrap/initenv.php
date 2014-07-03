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

!defined('APPBOX_PATH_BASE') && define('APPBOX_PATH_BASE',realpath(__DIR__.'/../../'));
!defined('APPBOX_PATH_APP') && define('APPBOX_PATH_APP',APPBOX_PATH_BASE.'/app');
!defined('APPBOX_PATH_DATA') && define('APPBOX_PATH_DATA',APPBOX_PATH_APP.'/data');
!defined('APPBOX_PATH_WEB') && define('APPBOX_PATH_WEB',APPBOX_PATH_APP.'/webroot');

use Pimple\Container;
use Nickfan\AppBox\Common\Usercache\ApcUsercache;
use Nickfan\AppBox\Common\Usercache\YacUsercache;
use Nickfan\AppBox\Common\Usercache\NullUsercache;
use Nickfan\AppBox\Config\Repository;
use Nickfan\AppBox\Config\DataRouteConf;
use Nickfan\AppBox\Instance\DataRouteInstance;

if(!function_exists('appbox')){
    function appbox(){
        static $app;
        if(empty($app)){
            $app = new Container();

            $app['path'] = array(
                'base'=>APPBOX_PATH_BASE,
                'app'=>APPBOX_PATH_APP,
                'storage'=>APPBOX_PATH_DATA,
                'public'=>APPBOX_PATH_WEB,
            );

            $app['path.base']= $app['path']['base'];
            $app['path.app']= $app['path']['app'];
            $app['path.storage']= $app['path']['storage'];
            $app['path.public']= $app['path']['public'];
            $userCacheObject = null;
            if(extension_loaded('apc')){
                $userCacheObject = new ApcUsercache;
            }elseif(extension_loaded('yac')){
                $userCacheObject = new YacUsercache;
            }else{
                $userCacheObject = new NullUsercache;
            }
            $app['usercache'] = $userCacheObject;

            $app['config'] = function ($app) {
                return new Repository($app['usercache'],$app['path.storage'].'/conf');
            };
            $app['datarouteconf'] = function ($app) {
                return new DataRouteConf($app['usercache'],$app['path.storage'].'/etc/local');
            };
            $app['datarouteinstance'] = function ($app) {
                return DataRouteInstance::getInstance($app['datarouteconf']);
            };
        }
        return $app;
    }
}
!isset($app) && $app = appbox();
