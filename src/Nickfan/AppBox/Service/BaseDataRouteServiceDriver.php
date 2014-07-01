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

use Nickfan\AppBox\Common\Exception\RuntimeException;
use Nickfan\AppBox\Config\DataRouteConf;
use Nickfan\AppBox\Instance\DataRouteInstance;

abstract class BaseDataRouteServiceDriver implements DataRouteServiceDriverInterface {

    protected static $instance = null;
    protected static $routeInstance = null;

    protected $driverKey = null;
    protected $routeKey = DataRouteConf::CONF_KEY_ROOT;

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
            $driverKey = DataRouteInstance::DRIVER_KEY_DEFAULT;
        }
        return array($className, $shortClassName, $driverKey);
    }

    /**
     * @param DataRouteInstance $instDataRouteInstance
     * @param null $driverKey
     * @return mixed
     */
    public static function factory(
        DataRouteInstance $instDataRouteInstance,
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
        DataRouteInstance $instDataRouteInstance,
        $driverKey = null
    ) {
        if (is_null($driverKey) && is_null($this->driverKey)) {
            $className = get_called_class();
            $className = substr($className, strrpos($className, '\\') + 1, -22); // -22 = strlen(DataRouteServiceDriver)
            if (!empty($className)) {
                $driverKey = lcfirst($className);
            } else {
                $driverKey = DataRouteInstance::DRIVER_KEY_DEFAULT;
            }
            $this->driverKey = $driverKey;
        }
        self::$routeInstance = $instDataRouteInstance;
        return $this;
    }

    public function getDataRouteInstance() {
        return self::$routeInstance;
    }

    public function setDataRouteInstance(DataRouteInstance $instDataRouteInstance) {
        self::$routeInstance = $instDataRouteInstance;
    }

    public function getDriverKey() {
        return $this->driverKey;
    }

    public function setDriverKey($driverKey = DataRouteInstance::DRIVER_KEY_DEFAULT) {
        $this->driverKey = $driverKey;
    }

    public function getRouteKey() {
        return $this->routeKey;
    }

    public function setRouteKey($routeKey = DataRouteConf::CONF_KEY_ROOT) {
        $this->routeKey = $routeKey;
    }

    public function getVendorSerivceInstanceSet($option = array(), $serviceInstance = null, $getAttrCallBack = null) {
        if (is_null($serviceInstance)) {
            $option += array(
                'driverKey' => $this->getDriverKey(),
                'routeMode' => DataRouteInstance::DATAROUTE_MODE_ATTR,
                'routeKey' => $this->getRouteKey(),
            );
            switch ($option['routeMode']) {
                case DataRouteInstance::DATAROUTE_MODE_IDSET:
                    if (!isset($option['routeIdSet'])) {
                        throw new RuntimeException('idset route mode require [routeIdSet]');
                    }
                    $routeIdSet = array(
                        'routeKey' => isset($option['routeIdSet']['routeKey']) ? $option['routeIdSet']['routeKey'] : $option['routeKey'],
                        'group' => isset($option['routeIdSet']['group']) ? $option['routeIdSet']['group'] : DataRouteConf::CONF_LABEL_INIT,
                    );
                    $driverInstance = self::$routeInstance->getRouteInstanceByConfSubset(
                        $option['driverKey'],
                        $routeIdSet['routeKey'],
                        $routeIdSet['group']
                    );
                    break;
                case DataRouteInstance::DATAROUTE_MODE_DIRECT:
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
                case DataRouteInstance::DATAROUTE_MODE_ATTR:
                default:
                    if (!isset($option['routeAttr'])) {
                        if (is_callable($getAttrCallBack)) {
                            $option['routeAttr'] = call_user_func($getAttrCallBack);
                        } elseif (is_array($getAttrCallBack)) {
                            $option['routeAttr'] = $getAttrCallBack;
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
            $serviceInstance = $driverInstance->getInstance();
        }
        return array($serviceInstance, $option);
    }

    public function callVendorServiceInstance(
        $method,
        $paramsList = array(),
        $option = array(),
        $serviceInstance = null,
        $getAttrCallBack = null
    ) {
        list($serviceInstance, $option) = $this->getVendorSerivceInstanceSet(
            $option,
            $serviceInstance,
            $getAttrCallBack
        );
        return call_user_func_array(array($serviceInstance, $method), $paramsList);
    }

    public function callStaticVendorServiceInstance(
        $method,
        $paramsList = array(),
        $option = array(),
        $serviceInstance = null,
        $getAttrCallBack = null
    ) {
        list($serviceInstance, $option) = $this->getVendorSerivceInstanceSet(
            $option,
            $serviceInstance,
            $getAttrCallBack
        );
        return call_user_func_array(array(get_class($serviceInstance), $method), $paramsList);
    }

    public function getRouteInstance(
        $routeKey = DataRouteConf::CONF_KEY_ROOT,
        $attributes = array(),
        $driverKey = null
    ) {
        empty($driverKey) && $driverKey = $this->getDriverKey();
        return self::$routeInstance->getRouteInstance($driverKey, $routeKey, $attributes);
    }

    public function getRouteInstanceRouteIdSet(
        $routeKey = DataRouteConf::CONF_KEY_ROOT,
        $attributes = array(),
        $driverKey = null
    ) {
        empty($driverKey) && $driverKey = $this->getDriverKey();
        return self::$routeInstance->getRouteInstanceRouteIdSet($driverKey, $routeKey, $attributes);
    }

    public function getRouteConfKeysByRouteKey($routeKey = DataRouteConf::CONF_KEY_ROOT, $driverKey = null) {
        empty($driverKey) && $driverKey = $this->getDriverKey();
        return self::$routeInstance->getRouteConfKeysByRouteKey($driverKey, $routeKey);
    }

    public function getRouteInstanceByConfSubset(
        $routeKey = DataRouteConf::CONF_KEY_ROOT,
        $subset = DataRouteConf::CONF_LABEL_INIT,
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