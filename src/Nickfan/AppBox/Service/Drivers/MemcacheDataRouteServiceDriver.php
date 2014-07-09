<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-30 13:52
 *
 */


namespace Nickfan\AppBox\Service\Drivers;


use Nickfan\AppBox\Service\BaseDataRouteServiceDriver;
use Nickfan\AppBox\Service\DataRouteServiceDriverInterface;

class MemcacheDataRouteServiceDriver extends BaseDataRouteServiceDriver implements DataRouteServiceDriverInterface {

    //protected static $driverKey = 'memcache';

    public function get($key, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $key,)
        );
        return $vendorInstance->get($key);
    }

    public function set($key, $val = '',$ttl=0, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $key,)
        );
        return $vendorInstance->set($key, $val,$ttl);
    }


    public function delete($key, $ttl=0, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $key,)
        );
        return $vendorInstance->delete($key,$ttl);
    }

    public function replace($key, $val = '',$ttl=0, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $key,)
        );
        return $vendorInstance->replace($key, $val,$ttl);
    }


    public function increment($key, $step=1, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $key,)
        );
        return $vendorInstance->increment($key,$step);
    }


    public function decrement($key, $step=1, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $key,)
        );
        return $vendorInstance->decrement($key,$step);
    }

    public function getMulti(array $keys=array(),$option=array(), $vendorInstance=NULL){
        return $this->callMultiGetVendorInstance($keys,'getMulti',array(),$option,$vendorInstance);
    }

    public function setMulti(array $items=array(),$ttl=0,$option=array(), $vendorInstance=NULL){
        return $this->callMultiGetVendorInstance($items,'setMulti',array($ttl),$option,$vendorInstance);
    }

}