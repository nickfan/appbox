<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-30 13:35
 *
 */


namespace Nickfan\AppBox\Service;

use Nickfan\AppBox\Common\AppConstants;
use Nickfan\AppBox\Common\Exception\RuntimeException;
use Nickfan\AppBox\Instance\DataRouteInstance;
use Nickfan\AppBox\Instance\DataRouteInstanceInterface;
use Nickfan\AppBox\Support\Util;

abstract class BaseDataRouteServiceDriver implements DataRouteServiceDriverInterface {

    protected static $instance = null;
    protected static $routeInstance = null;

    protected $driverKey = null;
    protected $routeKey = AppConstants::CONF_KEY_ROOT;

    public static function parseClassNameDriverKey() {
        $className = get_called_class();
        $shortClassName = substr(
            $className,
            strrpos($className, '\\') + 1,
            -22
        ); // -22 = strlen(DataRouteServiceDriver)
        if (!empty($shortClassName)) {
            $driverKey = lcfirst($shortClassName);
        } else {
            $driverKey = AppConstants::DRIVER_KEY_DEFAULT;
        }
        return array($className, $shortClassName, $driverKey);
    }

    /**
     * @param DataRouteInstance $instDataRouteInstance
     * @param null $driverKey
     * @return mixed
     */
    public static function factory(
        DataRouteInstanceInterface $instDataRouteInstance,
        $driverKey = null
    ) {
//        if (null === static::$instance) {
//            static::$instance = new static($instDataRouteInstance,$driverKey);
//        }
//        return static::$instance;
        $className = get_called_class();
        return new $className($instDataRouteInstance, $driverKey);
    }

    protected function __construct(
        DataRouteInstanceInterface $instDataRouteInstance,
        $driverKey = null
    ) {
        if (is_null($driverKey) && is_null($this->driverKey)) {
            $className = get_called_class();
            $className = substr($className, strrpos($className, '\\') + 1, -22); // -22 = strlen(DataRouteServiceDriver)
            if (!empty($className)) {
                $driverKey = lcfirst($className);
            } else {
                $driverKey = AppConstants::DRIVER_KEY_DEFAULT;
            }
            $this->driverKey = $driverKey;
        }
        self::$routeInstance = $instDataRouteInstance;
        $this->__init();
        return $this;
    }

    public function __init($params=array()){
        $params+=array(
            'routeKey'=>AppConstants::CONF_KEY_ROOT,
        );
        $this->setRouteKey($params['routeKey']);
    }
    public function getDataRouteInstance() {
        return self::$routeInstance;
    }

    public function setDataRouteInstance(DataRouteInstanceInterface $instDataRouteInstance) {
        self::$routeInstance = $instDataRouteInstance;
    }

    public function getDriverKey() {
        return $this->driverKey;
    }

    public function setDriverKey($driverKey = AppConstants::DRIVER_KEY_DEFAULT) {
        $this->driverKey = $driverKey;
    }

    public function getRouteKey() {
        return $this->routeKey;
    }

    public function setRouteKey($routeKey = AppConstants::CONF_KEY_ROOT) {
        $this->routeKey = $routeKey;
    }

    public function callMultiGetVendorInstance(array $keys,$method='',$params=array(),$rowPostCallback = null,$option=array(),$vendorInstance = null){
        $option += array(
            'driverKey' => $this->getDriverKey(),
            'routeMode' => AppConstants::DATAROUTE_MODE_ATTR,
            'routeKey' => $this->getRouteKey(),
            'routeAttrIdLabel'=>'id',
        );
        $resultDict = array();

        if(is_null($vendorInstance)){
            $routeAttr = array();
            // 根据各个属性分配到的池子中不同的分组
            $routeInstSetPool = array();
            foreach ($keys as $key) {
                $routeAttr = array(
                    $option['routeAttrIdLabel']=>$key,
                );
                $routeIdSet = self::$routeInstance->getRouteInstanceRouteIdSet(
                    $option['driverKey'],
                    $option['routeKey'],
                    $routeAttr
                );
                !isset($routeInstSetPool[$routeIdSet['group']]) && $routeInstSetPool[$routeIdSet['group']] = array('keys'=>array(),'idset'=>$routeIdSet);
                $keyHash = is_scalar($key)?$key:Util::getDataHashKey($key);
                !isset($routeInstSetPool[$routeIdSet['group']]['keys'][$keyHash]) && $routeInstSetPool[$routeIdSet['group']]['keys'][$keyHash] = $key;
            }
            $tplParams = $params;
            // 再分别根据各个分组将执行的结果返回
            if(!empty($routeInstSetPool)){
                foreach ($routeInstSetPool as $group => $groupSet) {
                    $rowParams = $tplParams;
                    $rowKeys = array_values($groupSet['keys']);
                    $routeIdSet = $groupSet['idset'];
                    $rowVendorInstance = self::$routeInstance->getRouteInstanceByConfSubset(
                        $option['driverKey'],
                        $routeIdSet['routeKey'],
                        $routeIdSet['group']
                    )->getInstance();
                    if(!empty($rowParams)){
                        array_unshift($rowParams,$rowKeys);
                    }else{
                        $rowParams = array($rowKeys);
                    }
                    if(!is_null($rowPostCallback)){
                        $midResult = call_user_func_array(array($rowVendorInstance,$method),$rowParams);
                        $result = call_user_func($rowPostCallback,$midResult);
                    }else{
                        $result = call_user_func_array(array($rowVendorInstance,$method),$rowParams);
                    }
                    if(!empty($result)){
                        $resultDict = array_merge($resultDict,$result);
                    }
                }
            }
        }else{
            if(!empty($params)){
                array_unshift($params,$keys);
            }else{
                $params = array($keys);
            }
            if(!is_null($rowPostCallback)){
                $midResult = call_user_func_array(array($vendorInstance,$method),$params);
                $resultDict = call_user_func($rowPostCallback,$midResult);
            }else{
                $resultDict = call_user_func_array(array($vendorInstance,$method),$params);
            }
        }
        return $resultDict;
    }

    public function callMultiSetVendorInstance(array $items,$method='',$params=array(),$rowPostCallback = null,$option=array(),$vendorInstance = null){
        $option += array(
            'driverKey' => $this->getDriverKey(),
            'routeMode' => AppConstants::DATAROUTE_MODE_ATTR,
            'routeKey' => $this->getRouteKey(),
            'routeAttrIdLabel'=>'id',
            'simplifyResult'=>true,
            'defaultStatus'=>true,
        );
        $resultDict = array();
        if(is_null($vendorInstance)){
            $keys = array_keys($items);
            $routeAttr = array();
            // 根据各个属性分配到的池子中不同的分组
            $routeInstSetPool = array();
            foreach ($keys as $key) {
                $routeAttr = array(
                    $option['routeAttrIdLabel']=>$key,
                );
                $routeIdSet = self::$routeInstance->getRouteInstanceRouteIdSet(
                    $option['driverKey'],
                    $option['routeKey'],
                    $routeAttr
                );
                !isset($routeInstSetPool[$routeIdSet['group']]) && $routeInstSetPool[$routeIdSet['group']] = array('items'=>array(),'idset'=>$routeIdSet);
                !isset($routeInstSetPool[$routeIdSet['group']]['items'][$key]) && $routeInstSetPool[$routeIdSet['group']]['items'][$key] = $items[$key];
            }
            $tplParams = $params;
            // 再分别根据各个分组将执行的结果返回
            if(!empty($routeInstSetPool)){
                foreach ($routeInstSetPool as $group => $groupSet) {
                    $rowParams = $tplParams;
                    $rowItems = $groupSet['items'];
                    $rowKeys = array_keys($groupSet['items']);
                    $routeIdSet = $groupSet['idset'];
                    $rowVendorInstance = self::$routeInstance->getRouteInstanceByConfSubset(
                        $option['driverKey'],
                        $routeIdSet['routeKey'],
                        $routeIdSet['group']
                    )->getInstance();
                    if(!empty($rowParams)){
                        array_unshift($rowParams,$rowItems);
                    }else{
                        $rowParams = array($rowItems);
                    }


                    if(!is_null($rowPostCallback)){
                        $midResult = call_user_func_array(array($rowVendorInstance,$method),$rowParams);
                        $result = call_user_func($rowPostCallback,$midResult);
                    }else{
                        $result = call_user_func_array(array($rowVendorInstance,$method),$rowParams);
                    }

                    if(!empty($result)){
                        $rowResult = array_fill_keys($rowKeys,$result);
                        $resultDict = array_merge($resultDict,$rowResult);
                    }
                }
            }
            if($option['simplifyResult']==true){
                $resultDict = array_reduce($resultDict,function($carry,$item){ return $carry && $item;},$option['defaultStatus']);
            }
        }else{
            if(!empty($params)){
                array_unshift($params,$items);
            }else{
                $params = array($items);
            }
            if(!is_null($rowPostCallback)){
                $midResult = call_user_func_array(array($vendorInstance,$method),$params);
                $resultDict = call_user_func($rowPostCallback,$midResult);
            }else{
                $resultDict = call_user_func_array(array($vendorInstance,$method),$params);
            }
        }
        return $resultDict;
    }
    public function getDriverInstanceSet($option=array(),$getAttrCallBack = null){
        $driverInstance = null;
        $option += array(
            'driverKey' => $this->getDriverKey(),
            'routeMode' => AppConstants::DATAROUTE_MODE_ATTR,
            'routeKey' => $this->getRouteKey(),
        );
        switch ($option['routeMode']) {
            case AppConstants::DATAROUTE_MODE_IDSET:
                if (!isset($option['routeIdSet'])) {
                    throw new RuntimeException('idset route mode require [routeIdSet]');
                }
                $routeIdSet = array(
                    'routeKey' => isset($option['routeIdSet']['routeKey']) ? $option['routeIdSet']['routeKey'] : $option['routeKey'],
                    'group' => isset($option['routeIdSet']['group']) ? $option['routeIdSet']['group'] : AppConstants::CONF_LABEL_INIT,
                );
                $driverInstance = self::$routeInstance->getRouteInstanceByConfSubset(
                    $option['driverKey'],
                    $routeIdSet['routeKey'],
                    $routeIdSet['group']
                );
                break;
            case AppConstants::DATAROUTE_MODE_DIRECT:
                if (!isset($option['routeSettings'])) {
                    throw new RuntimeException('direct route mode require [routeSettings]');
                }
                $routeIdSet = isset($option['routeIdSet']) ? $option['routeIdSet'] : array();
                $driverInstance = self::$routeInstance->getDriverInstance(
                    $option['driverKey'],
                    $option['routeSettings'],
                    $routeIdSet
                );
                break;
            case AppConstants::DATAROUTE_MODE_ATTR:
            default:
                if (!isset($option['routeAttr'])) {
                    if (is_callable($getAttrCallBack)) {
                        $option['routeAttr'] = call_user_func($getAttrCallBack);
                    } elseif (is_array($getAttrCallBack)) {
                        $option['routeAttr'] = $getAttrCallBack;
                    } elseif (is_null($getAttrCallBack)) {
                        $option['routeAttr'] = array();
                    } else {
                        throw new RuntimeException('direct route mode require [routeAttr]');
                    }
                }
                $driverInstance = self::$routeInstance->getRouteInstance(
                    $option['driverKey'],
                    $option['routeKey'],
                    $option['routeAttr']
                );
                break;
        }
        return array($driverInstance,$option);
    }

    protected function buildRouteAttrCallbackByQueryStruct($queryStruct = array(),$option=array()){
        $routeSetVector = array();
        $option += array(
            'routeMode' => AppConstants::DATAROUTE_MODE_ATTR,
        );
        switch ($option['routeMode']) {
            case AppConstants::DATAROUTE_MODE_IDSET:
                break;
            case AppConstants::DATAROUTE_MODE_DIRECT:
                break;
            case AppConstants::DATAROUTE_MODE_ATTR:
            default:
                if (!isset($option['routeAttr'])) {
                    if(!empty($queryStruct) && isset($queryStruct['conditionKey'])){
                        if(isset($queryStruct['conditionKey']['id'])){
                            $routeSetVector['id']=$queryStruct['conditionKey']['id'];
                        }
                        if(isset($queryStruct['conditionKey']['_id'])){
                            $routeSetVector['_id']=$queryStruct['conditionKey']['_id'];
                        }
                        if(isset($queryStruct['conditionKey']['idstr'])){
                            $routeSetVector['idstr']=$queryStruct['conditionKey']['idstr'];
                        }
                        if(isset($queryStruct['conditionKey']['key'])){
                            $routeSetVector['key']=$queryStruct['conditionKey']['key'];
                        }
                        if(isset($queryStruct['conditionKey']['pid'])){
                            $routeSetVector['pid']=$queryStruct['conditionKey']['pid'];
                        }
                    }
                }
                break;
        }
        return $routeSetVector;
    }
    
    protected function buildRouteAttrCallbackByDict($dict = array(),$option=array()){
        $routeSetVector = array();
        $option += array(
            'routeMode' => AppConstants::DATAROUTE_MODE_ATTR,
        );
        switch ($option['routeMode']) {
            case AppConstants::DATAROUTE_MODE_IDSET:
                break;
            case AppConstants::DATAROUTE_MODE_DIRECT:
                break;
            case AppConstants::DATAROUTE_MODE_ATTR:
            default:
                if (!isset($option['routeAttr'])) {
                    if(!empty($dict)){
                        if(isset($dict['id'])){
                            $routeSetVector['id']=$dict['id'];
                        }
                        if(isset($dict['_id'])){
                            $routeSetVector['_id']=$dict['_id'];
                        }
                        if(isset($dict['idstr'])){
                            $routeSetVector['idstr']=$dict['idstr'];
                        }
                        if(isset($dict['key'])){
                            $routeSetVector['key']=$dict['key'];
                        }
                        if(isset($dict['pid'])){
                            $routeSetVector['pid']=$dict['pid'];
                        }
                    }
                }
                break;
        }
        return $routeSetVector;
    }

    public function getVendorInstanceSet($option = array(), $vendorInstance = null, $getAttrCallBack = null) {
        if (is_null($vendorInstance)) {
            $option += array(
                'driverKey' => $this->getDriverKey(),
                'routeMode' => AppConstants::DATAROUTE_MODE_ATTR,
                'routeKey' => $this->getRouteKey(),
            );
            switch ($option['routeMode']) {
                case AppConstants::DATAROUTE_MODE_IDSET:
                    if (!isset($option['routeIdSet'])) {
                        throw new RuntimeException('idset route mode require [routeIdSet]');
                    }
                    $routeIdSet = array(
                        'routeKey' => isset($option['routeIdSet']['routeKey']) ? $option['routeIdSet']['routeKey'] : $option['routeKey'],
                        'group' => isset($option['routeIdSet']['group']) ? $option['routeIdSet']['group'] : AppConstants::CONF_LABEL_INIT,
                    );
                    $driverInstance = self::$routeInstance->getRouteInstanceByConfSubset(
                        $option['driverKey'],
                        $routeIdSet['routeKey'],
                        $routeIdSet['group']
                    );
                    break;
                case AppConstants::DATAROUTE_MODE_DIRECT:
                    if (!isset($option['routeSettings'])) {
                        throw new RuntimeException('direct route mode require [routeSettings]');
                    }
                    $routeIdSet = isset($option['routeIdSet']) ? $option['routeIdSet'] : array();
                    $driverInstance = self::$routeInstance->getDriverInstance(
                        $option['driverKey'],
                        $option['routeSettings'],
                        $routeIdSet
                    );
                    break;
                case AppConstants::DATAROUTE_MODE_ATTR:
                default:
                    if (!isset($option['routeAttr'])) {
                        if (is_callable($getAttrCallBack)) {
                            $option['routeAttr'] = call_user_func($getAttrCallBack);
                        } elseif (is_array($getAttrCallBack)) {
                            $option['routeAttr'] = $getAttrCallBack;
                        } elseif (is_null($getAttrCallBack)) {
                            $option['routeAttr'] = array();
                        } else {
                            throw new RuntimeException('attr route mode require [routeAttr]');
                        }
                    }
                    $driverInstance = self::$routeInstance->getRouteInstance(
                        $option['driverKey'],
                        $option['routeKey'],
                        $option['routeAttr']
                    );
                    break;
            }
            $vendorInstance = $driverInstance->getInstance();
        }
        return array($vendorInstance, $option);
    }

    public function callVendorInstance(
        $method,
        $paramsList = array(),
        $option = array(),
        $vendorInstance = null,
        $getAttrCallBack = null
    ) {
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            $getAttrCallBack
        );
        return call_user_func_array(array($vendorInstance, $method), $paramsList);
    }

    public function callStaticVendorInstance(
        $method,
        $paramsList = array(),
        $option = array(),
        $vendorInstance = null,
        $getAttrCallBack = null
    ) {
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            $getAttrCallBack
        );
        return call_user_func_array(array(get_class($vendorInstance), $method), $paramsList);
    }

    public function getRouteInstance(
        $routeKey = AppConstants::CONF_KEY_ROOT,
        $attributes = array(),
        $driverKey = null
    ) {
        empty($driverKey) && $driverKey = $this->getDriverKey();
        return self::$routeInstance->getRouteInstance($driverKey, $routeKey, $attributes);
    }

    public function getRouteInstanceRouteIdSet(
        $routeKey = AppConstants::CONF_KEY_ROOT,
        $attributes = array(),
        $driverKey = null
    ) {
        empty($driverKey) && $driverKey = $this->getDriverKey();
        return self::$routeInstance->getRouteInstanceRouteIdSet($driverKey, $routeKey, $attributes);
    }

    public function getRouteConfKeysByRouteKey($routeKey = AppConstants::CONF_KEY_ROOT, $driverKey = null) {
        empty($driverKey) && $driverKey = $this->getDriverKey();
        return self::$routeInstance->getRouteConfKeysByRouteKey($driverKey, $routeKey);
    }

    public function getRouteInstanceByConfSubset(
        $routeKey = AppConstants::CONF_KEY_ROOT,
        $subset = AppConstants::CONF_LABEL_INIT,
        $driverKey = null
    ) {
        empty($driverKey) && $driverKey = $this->getDriverKey();
        return self::$routeInstance->getRouteInstanceByConfSubset($driverKey, $routeKey, $subset);
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
} 