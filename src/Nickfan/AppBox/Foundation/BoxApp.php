<?php
/**
 * Description
 *
 * @project AppBox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-19 10:46
 *
 */
namespace Nickfan\AppBox\Foundation;

use Illuminate\Container\Container;
use Nickfan\AppBox\Support\BoxUtil;
use Nickfan\AppBox\Common\Usercache\ApcBoxUsercache;
use Nickfan\AppBox\Common\Usercache\YacBoxUsercache;
use Nickfan\AppBox\Common\Usercache\NullBoxUsercache;
use Nickfan\AppBox\Common\Exception\UnexpectedValueException;
class BoxApp extends Container{

    public function inst(){
        return $this;
    }
    /**
     * Quick debugging of any variable. Any number of parameters can be set.
     *
     * @return  string
     */
    public static function debug() {
        if (func_num_args() === 0) {
            return null;
        }
        // Get params
        $params = func_get_args();
        $printBool = true;
        if(func_num_args() > 1){
            $printBool = boolval(array_shift($params));
        }
        $output = array();
        foreach ($params as $var) {
            $output[] = '(' . gettype($var) . ') ' . var_export($var, true) . '';
        }
        if (php_sapi_name() == 'cli') {
            if ($printBool == true) {
                print(implode("\n", $output));
            } else {
                return implode("\n", $output);
            }
        } else {
            if ($printBool == true) {
                print('<pre>' . implode("</pre>\n<pre>", $output) . '</pre>');
            } else {
                return '<pre>' . implode("</pre>\n<pre>", $output) . '</pre>';
            }
        }
        return null;
    }

    /**
     * Create a new Illuminate application instance.
     *
     * @return void
     */
    public function __construct(){
        $this->registerBaseBindings();
    }

    public static function appbox(\Closure $callback=null){
        $app = null;
        if (!is_callable($callback)) {
            throw new UnexpectedValueException('init script error');
        }else{
            $app = call_user_func($callback);
        }
        return $app;
    }

    /**
     * Register the basic bindings into the container.
     *
     * @return void
     */
    protected function registerBaseBindings(){
        $this->instance('Illuminate\Container\Container', $this);
    }

    /**
     * Bind the installation paths to the application.
     *
     * @param  array $paths
     * @return void
     */
    public function bindInstallPaths(array $paths) {
        $this->instance('path', realpath($paths['app']));

        // Here we will bind the install paths into the container as strings that can be
        // accessed from any point in the system. Each path key is prefixed with path
        // so that they have the consistent naming convention inside the container.
        foreach (BoxUtil::array_except($paths, array('app')) as $key => $value) {
            $this->instance("path.{$key}", realpath($value));
        }
    }


    /**
     * Register the core class aliases in the container.
     *
     * @return void
     */
    public function registerCoreContainerAliases() {
        $aliases = array(
            'app' => 'Nickfan\AppBox\Foundation\BoxApp',
            'dict' => 'Nickfan\AppBox\Config\BoxDictionary',
            'boxconf' => 'Nickfan\AppBox\Config\BoxRepository',
            'boxrouteconf' => 'Nickfan\AppBox\Config\BoxRouteConf',
            'boxrouteinst' => 'Nickfan\AppBox\Instance\DataRouteInstance',
        );

        foreach ($aliases as $key => $alias) {
            $this->alias($key, $alias);
        }
    }


    public static function verifyRepositoryPath($confPath=''){
        $realpath = realpath($confPath);
        if(file_exists($realpath) && is_dir($realpath)){
            return true;
        }
        return false;
    }

    public static function verifyRouteConfPath($routeConfPath=''){
        $realpath = realpath($routeConfPath);
        if(file_exists($realpath) && is_dir($realpath)){
            if(file_exists($realpath.'/dsn')
                && is_dir($realpath.'/dsn')
                && file_exists($realpath.'/route')
                && is_dir($realpath.'/route')
            ){
                return true;
            }
        }
        return false;
    }

    public function setRepositoryPath($confPath=''){

    }

    public static function makeUserCacheInstance(){
        if(extension_loaded('apc')){
            $usercacheInterface = new ApcBoxUsercache;
        }elseif(extension_loaded('yac')){
            $usercacheInterface = new YacBoxUsercache;
        }else{
            $usercacheInterface = new NullBoxUsercache;
        }
        return $usercacheInterface;
    }

    public static function buildRealPaths(array $paths){
        $realPaths = array();
        foreach ($paths as $key => $value) {
            $realPaths[$key] = realpath($value);
        }
        return $realPaths;
    }


    /**
     * Dynamically access application services.
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key) {
        return $this[$key];
    }

    /**
     * Dynamically set application services.
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function __set($key, $value) {
        $this[$key] = $value;
    }

}
