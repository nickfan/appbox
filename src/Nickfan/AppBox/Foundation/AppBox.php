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

use Pimple\Container;
use Nickfan\AppBox\Support\BoxUtil;

class AppBox{
    private static $instance = null;
    private $box = null;
    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct() {
        $this->box = new Container();
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

    public static function box(){
        $instance = self::getInstance();
        return $instance->box;
    }

    public static function make($abstract=''){
        $box = self::box();
        return isset($box[$abstract])?$box[$abstract]:null;
    }
    public static function register($abstract='',$value=null){
        $box = self::box();
        $box[$abstract] = $value;
    }
}