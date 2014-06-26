<?php
/**
 * Description
 *
 * @project appbox
 * @package 
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-11 11:07
 *
 */



namespace Nickfan\MyApp\Dispatcher;

class MyDispatcher {

    protected static $app;

    /**
     * Returns the *Singleton* instance of this class.
     *
     * @staticvar Singleton $instance The *Singleton* instances of this class.
     *
     * @return Singleton The *Singleton* instance.
     */
    public static function getInstance($app=null)
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static($app);
        }

        return $instance;
    }

    public static function setApp($app){
        static::$app = $app;
    }
    public static function getApp(){
        return static::$app;
    }
    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct($app){
        if(!is_null($app)){
            static::setApp($app);
        }
        $this->init();
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     */
    private function __clone(){
    }

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     *
     * @return void
     */
    private function __wakeup(){
    }

    public $pkg = null;
    public $mod = null;
    public $act = null;
    public static $app_root = '';
    public static $data_root = '';
    public function init($params=array()){
        $params+=array(
            'pkg'=>isset($_GET['pkg'])?trim($_GET['pkg']):'index',
            'mod'=>isset($_GET['mod'])?trim($_GET['mod']):'index',
            'act'=>isset($_GET['act'])?trim($_GET['act']):'index',
            'app_root'=>isset(static::$app['path.app'])?static::$app['path.app']:'',
            'data_root'=>isset(static::$app['path.storage'])?static::$app['path.storage']:'',
        );
        $this->pkg = $params['pkg'];
        $this->mod = $params['mod'];
        $this->act = $params['act'];
        self::$app_root = $params['app_root'];
        self::$data_root = $params['data_root'];
    }
    public function run(){

        $controllername = '\\Nickfan\\MyApp\\Package\\'.$this->pkg.'\\'.$this->mod;
        if(class_exists($controllername)){
            $controllerObj = new $controllername(self::getInstance());
            return call_user_func(array($controllerObj,$this->act));
        }else{
            throw new \BadMethodCallException('undefined method',500);
        }

    }
} 