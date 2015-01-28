<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2015-01-22 14:39
 *
 */
use Nickfan\AppBox\Common\Usercache\AutoBoxUsercache;
use Nickfan\AppBox\Config\BoxDictionary;
use Nickfan\AppBox\Config\BoxRouteConf;
use Nickfan\AppBox\Config\BoxRepository;
use Nickfan\AppBox\Instance\BoxRouteInstance;
use Nickfan\AppBox\Support\Facades\Facade;
use Nickfan\AppBox\Foundation\AliasLoader;
!isset($boxapp) && $boxapp = Nickfan\AppBox\Foundation\BoxApp::appbox(function(){
    static $boxapp;
    if(empty($boxapp)){
        $boxapp = new Nickfan\AppBox\Foundation\BoxApp;

        /*
        |--------------------------------------------------------------------------
        | Bind Paths
        |--------------------------------------------------------------------------
        |
        | Here we are binding the paths configured in paths.php to the app. You
        | should not be changing these here. If you need to change these you
        | may do so within the paths.php file and they will be bound here.
        |
        */

        $boxapp->bindInstallPaths(require __DIR__ . '/paths.php');

        /*
        |--------------------------------------------------------------------------
        | Bind The Application In The Container
        |--------------------------------------------------------------------------
        |
        | This may look strange, but we actually want to bind the app into itself
        | in case we need to Facade test an application. This will allow us to
        | resolve the "app" key out of this container for this app's facade.
        |
        */

        $boxapp->instance('app', $boxapp);

        /*
        |--------------------------------------------------------------------------
        | Load The Illuminate Facades
        |--------------------------------------------------------------------------
        |
        | The facades provide a terser static interface over the various parts
        | of the application, allowing their methods to be accessed through
        | a mixtures of magic methods and facade derivatives. It's slick.
        |
        */

        Facade::clearResolvedInstances();

        Facade::setFacadeApplication($boxapp);

        /*
        |--------------------------------------------------------------------------
        | Register Facade Aliases To Full Classes
        |--------------------------------------------------------------------------
        |
        | By default, we use short keys in the container for each of the core
        | pieces of the framework. Here we will register the aliases for a
        | list of all of the fully qualified class names making DI easy.
        |
        */

        $boxapp->registerCoreContainerAliases();
        //$userCacheObject = new AutoBoxUsercache();
        $userCacheObject = $boxapp->makeUserCacheInstance();
        $boxapp->instance('usercache', $userCacheObject);
        $boxapp->instance('boxdict', $dict = new BoxDictionary(array()));
        $boxapp->instance('boxconf', $config = new BoxRepository($boxapp['path.storage'] . '/conf', $userCacheObject));
        $boxapp->instance('boxrouteconf', $routeconf = new BoxRouteConf($boxapp['path.storage'] . '/etc/local', $userCacheObject));
        $boxapp->instance('boxrouteinst', $routeinstance = BoxRouteInstance::getInstance($routeconf));

        date_default_timezone_set($boxapp['boxconf']['app']['timezone']);
        /*
        |--------------------------------------------------------------------------
        | Register The Alias Loader
        |--------------------------------------------------------------------------
        |
        | The alias loader is responsible for lazy loading the class aliases setup
        | for the application. We will only register it if the "config" service
        | is bound in the application since it contains the alias definitions.
        |
        */

        AliasLoader::getInstance($boxapp['boxconf']['app']['aliases'])->register();

    }
    return $boxapp;
});
return $boxapp;
