<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-17 17:19
 *
 */


namespace Nickfan\AppBox\Common\Usercache;


class AutoBoxUsercache extends BoxBaseUsercache implements BoxUsercacheInterface {
    protected $usercacheInstance = null;
    protected $cachedriverkey = 'null';
    public function __construct($option = array()) {
        $option += array(
            'driver' => 'auto', // auto | none | apc | yac | redis,
            'autoseq' => array(
                'apc',
                'yac',
            ),
        );
        switch($option['driver']){
            case 'auto':
                $gotdriverkey = 'null';
                foreach ($option['autoseq'] as $driverkey) {
                    if($driverkey=='apc' || extension_loaded('apc')){
                        $gotdriverkey = 'apc';
                    }elseif($driverkey=='yac' || extension_loaded('yac')){
                        $gotdriverkey = 'yac';
                    }elseif($driverkey=='redis' || extension_loaded('redis')){
                        $gotdriverkey = 'redis';
                    }
                }
                switch($gotdriverkey){
                    case 'apc':
                        $this->usercacheInstance =  new ApcBoxUsercache($option);
                        $this->cachedriverkey = 'apc';
                        break;
                    case 'yac':
                        $this->usercacheInstance =  new YacBoxUsercache($option);
                        $this->cachedriverkey = 'yac';
                        break;
                    case 'redis':
                        $this->usercacheInstance =  new RedisBoxUsercache($option);
                        $this->cachedriverkey = 'redis';
                        break;
                    case 'null':
                    case 'none':
                    default:
                        $this->usercacheInstance =  new NullBoxUsercache($option);
                        $this->cachedriverkey = 'null';
                        break;
                }
                break;
            case 'apc':
                    $this->usercacheInstance =  new ApcBoxUsercache($option);
                    $this->cachedriverkey = 'apc';
                break;
            case 'yac':
                    $this->usercacheInstance =  new YacBoxUsercache($option);
                    $this->cachedriverkey = 'yac';
                break;
            case 'redis':
                    $this->usercacheInstance =  new RedisBoxUsercache($option);
                    $this->cachedriverkey = 'redis';
                break;
            case 'null':
            case 'none':
                default:
                    $this->usercacheInstance =  new NullBoxUsercache($option);
                    $this->cachedriverkey = 'null';
                break;
        }
        return $this;
    }

    public function getCacheDriverKey() {
        return $this->cachedriverkey;
    }
    public function setOption($option = array()) {
        return call_user_func_array(array($this->usercacheInstance,__FUNCTION__),func_get_args());
    }

    public function getOption() {
        return call_user_func_array(array($this->usercacheInstance,__FUNCTION__),func_get_args());
    }

    public function get($key, $option = array()) {
        return call_user_func_array(array($this->usercacheInstance,__FUNCTION__),func_get_args());
    }

    public function set($key, $val, $option = array()) {
        return call_user_func_array(array($this->usercacheInstance,__FUNCTION__),func_get_args());
    }

    public function del($key, $option = array()) {
        return call_user_func_array(array($this->usercacheInstance,__FUNCTION__),func_get_args());
    }

    public function exits($key, $option = array()) {
        return call_user_func_array(array($this->usercacheInstance,__FUNCTION__),func_get_args());
    }

    public function flush($option = array()) {
        return call_user_func_array(array($this->usercacheInstance,__FUNCTION__),func_get_args());
    }

}
