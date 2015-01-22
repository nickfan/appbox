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

use Nickfan\AppBox\Support\BoxUtil;
use Nickfan\AppBox\Common\Exception\UnexpectedValueException;

use Nickfan\AppBox\Config\BoxRouteConf;
use Nickfan\AppBox\Foundation\BoxSettings;
use Nickfan\AppBox\Config\BoxRepository;
use Nickfan\AppBox\Common\Usercache\BoxUsercacheInterface;
use Nickfan\AppBox\Common\Usercache\ApcBoxUsercache;
use Nickfan\AppBox\Common\Usercache\YacBoxUsercache;
use Nickfan\AppBox\Common\Usercache\NullBoxUsercache;

class AppBox extends Container{
    protected static $instance = null;
    protected $box = null;
    protected $settings = null;
    protected $userCacheInstance = null;
    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct() {
        $this->settings = new BoxSettings();
//        $this->box = new Container();
        return $this;
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     */
    private function __clone() {
    }

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     *
     * @return void
     */
    private function __wakeup() {
    }

    /**
     * Returns the *Singleton* instance of this class.
     *
     * @staticvar Singleton $instance The *Singleton* instances of this class.
     *
     * @return Singleton The *Singleton* instance.
     */
    public static function getInstance() {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public static function app(){
        return new Container();
    }

    public static function box(){
        $instance = static::getInstance();
        return $instance->getBox();
    }
    public function getBox(){
        return $this->box;
    }
    public function setBox(Container $container){
        $this->box = $container;
    }

    public static function settings(){
        $instance = static::getInstance();
        return $instance->getBox();
    }
    public function getSettings(){
        return $this->settings;
    }
    public function setSettings(BoxSettings $settings){
        $this->settings = $settings;
    }
    public static function instSettings(BoxSettings $settings){
        $instance = static::getInstance();
        $instance->setSettings($settings);
    }
    public static function getInstSetVar($key){
        $instance = static::getInstance();
        return $instance->getSetVar($key);
    }

    public function getSetVar($key){
        return $this->settings->get($key);
    }
    public function setSetVar($key,$val){
        $this->settings->set($key,$val);
    }

    public static function make($abstract=''){
        $instance = static::getInstance();
        $box = $instance->getBox();
        return isset($box[$abstract])?$box[$abstract]:null;
    }
    public static function register($abstract='',$value=null){
        $instance = static::getInstance();
        $box = $instance->getBox();
        $box[$abstract] = $value;
    }
    public static function init(\Closure $callback=null){
        $instance = static::getInstance();
        if(!is_null($callback)){
            if (!is_callable($callback)) {
                throw new UnexpectedValueException('init script error');
            }else{
                $box = call_user_func($callback);
                if($box instanceof Container){
                    $instance->setBox($box);
                }
            }
        }
        return $instance;
    }

    public static function verifyRepositoryPath($confPath=''){
        $realpath = realpath($confPath);
        if(file_exists($realpath) && is_dir($realpath)){
            return true;
        }
        return false;
    }
    public static function setRepositoryPath($confPath=''){
        $instance = static::getInstance();
        $app = $instance->getBox();
        $paths = $instance->getSetVar('path');
        if($confPath!=$paths['conf'] && self::verifyRepositoryPath($confPath)){
            $paths['conf'] = $confPath;
            $instance->setSetVar('path',$paths);
            $app->extend('conf', function ($app,$paths) {
                return new BoxRepository($paths['conf'],$app['usercache']);
            });
        }
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

    public static function setRouteConfPath($routeConfPath=''){
        $instance = static::getInstance();
        $app = $instance->getBox();
        $paths = $instance->getSetVar('path');
        if($routeConfPath!=$paths['routeconf'] && self::verifyRouteConfPath($routeConfPath)){
            $paths['routeconf'] = $routeConfPath;
            $instance->setSetVar('path',$paths);
            $app->extend('routeconf', function ($app,$paths) {
                return new BoxRouteConf($paths['routeconf'],$app['usercache']);
            });
            $app->extend('routeinst', function ($app) {
                return BoxRouteInstance::getInstance($app['routeconf']);
            });
        }
    }


    public function getUserCacheInstance(){
        return $this->userCacheInstance;
    }
    public function setUserCacheInstance(BoxUsercacheInterface $usercacheInterface = null,$detect=true){
        if(is_null($usercacheInterface) && $detect==true){
            $usercacheInterface =  self::makeUserCacheInstance();
        }
        $this->userCacheInstance = $usercacheInterface;
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
}