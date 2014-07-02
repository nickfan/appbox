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

class RedisDataRouteServiceDriver extends BaseDataRouteServiceDriver implements DataRouteServiceDriverInterface {

    //protected static $driverKey = 'redis';

    public function get($key, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $key,)
        );
        return $vendorInstance->get($key);
    }

    public function set($key, $val = '', $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $key,)
        );
        return $vendorInstance->set($key, $val);
    }


    /**
     * 生成序列id
     * @param string $objectName
     * @param array $option
     * @param null $vendorInstance
     * @return int
     */
    public function nextSeq($objectName = '',$option = array(), $vendorInstance = null){
        $option += array(
            'routeKey'=>'Seq',
            'init' => 0,
            'step' => 1,
        );
        empty($objectName) && $objectName = $this->getRouteKey();
        $seqModLabel = lcfirst($option['routeKey']);

        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $objectName,)
        );


        $respInfo = $vendorInstance->multi()
            ->setnx($seqModLabel.'_'.$objectName, $option['init'])
            ->incrBy($seqModLabel.'_'.$objectName,$option['step'])
            ->get($seqModLabel.'_'.$objectName)
            ->exec();

        if(isset($respInfo[2]) && !empty($respInfo[2])){
            return intval($respInfo[2]);
        }else{
            return $option['init'];
        }
    }

    /**
     * 获取当前序列id
     * @param string $objectName
     * @param array $option
     * @param null $vendorInstance
     * @return int
     */
    public function currentSeq($objectName = '',$option = array(), $vendorInstance = null){
        $option += array(
            'routeKey'=>'Seq',
            'init' => 0,
            //'step' => 1,
        );
        empty($objectName) && $objectName = $this->getRouteKey();
        $seqModLabel = lcfirst($option['routeKey']);

        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $objectName,)
        );
        $respInfo = $vendorInstance->multi()
            ->setnx($seqModLabel.'_'.$objectName, $option['init'])
            ->get($seqModLabel.'_'.$objectName)
            ->exec();

        if(isset($respInfo[1]) && !empty($respInfo[1])){
            return intval($respInfo[1]);
        }else{
            return $option['init'];
        }
    }

    /**
     * 设定序列id
     * @param string $objectName
     * @param array $option
     * @param null $vendorInstance
     * @return int
     */
    public function setSeq($objectName = '',$option = array(), $vendorInstance = null){
        $option += array(
            'routeKey'=>'Seq',
            'init' => 0,
            'step' => 1,
        );
        empty($objectName) && $objectName = $this->getRouteKey();
        $seqModLabel = lcfirst($option['routeKey']);

        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $objectName,)
        );


        $respInfo = $vendorInstance->multi()
            ->set($seqModLabel.'_'.$objectName, $option['init'])
            ->get($seqModLabel.'_'.$objectName)
            ->exec();

        if(isset($respInfo[2]) && !empty($respInfo[2])){
            return intval($respInfo[2]);
        }else{
            return $option['init'];
        }
    }
} 