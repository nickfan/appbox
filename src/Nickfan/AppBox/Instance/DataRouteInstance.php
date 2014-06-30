<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-25 17:37
 *
 */


namespace Nickfan\AppBox\Instance;

use Nickfan\AppBox\Common\Exception\DataRouteInstanceException;
use Nickfan\AppBox\Config\DataRouteConf;

class DataRouteInstance {
    const DRIVER_KEY_DEFAULT = 'cfg';
    const DATAROUTE_MODE_ATTR = 0;      // dataroute mode by attributes
    const DATAROUTE_MODE_IDSET = 1;      // dataroute mode by routeIdSet
    const DATAROUTE_MODE_DIRECT = 3;     // dataroute mode by directsettings

    private static $routeConf = null;
    private static $setShutdownHandler = true;
    private static $instancePools = array();

    /**
     * Returns the *Singleton* instance of this class.
     *
     * @staticvar Singleton $instance The *Singleton* instances of this class.
     *
     * @return Singleton The *Singleton* instance.
     */
    public static function getInstance(DataRouteConf $routeConf=null) {
        static $instance = null;
        if (null === $instance) {
            $instance = new static($routeConf);
        }
        return $instance;
    }

    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct(DataRouteConf $routeConf=null) {
        self::$routeConf = $routeConf;
        self::setShutDownHandler();
    }

    public static function setRouteConf(DataRouteConf $routeConf){
        self::$routeConf = $routeConf;
    }
    public static function getRouteConf(){
        return self::$routeConf;
    }
    public static function setShutDownHandler() {
        if (self::$setShutdownHandler == true) {
            register_shutdown_function(array('\\Nickfan\\AppBox\\Instance\\DataRouteInstance', 'close'));
        }
    }

    /**
     * get Data Routed Driver Instance By routeKey and id vector (data attributes)
     * @param string $driverKey
     * @param string $routeKey
     * @param array $attributes
     * @return bool
     * @throws \Nickfan\AppBox\Common\Exception\DataRouteInstanceException
     */
    public function getRouteInstance($driverKey = self::DRIVER_KEY_DEFAULT, $routeKey = DataRouteConf::CONF_KEY_ROOT, $attributes = array()) {
        $driverKey = lcfirst($driverKey);
        $driverName = ucfirst($driverKey);
        $routeIdSet = self::$routeConf->getRouteConfKeySetByScript($driverKey, $routeKey, $attributes);
        $dataRouteInstance = self::getPoolInstanceByRouteIdSet($driverKey, $routeIdSet);
        if ($dataRouteInstance !== false) {
            return $dataRouteInstance;
        }
        $driverClassName = '\\Nickfan\\AppBox\\Instance\\Drivers\\' . $driverName . 'DataRouteInstanceDriver';

        if (class_exists($driverClassName)) {
            $settings = self::$routeConf->getRouteConfByRouteConfKeySet($driverKey, $routeIdSet);
            $driverClassInstance = new $driverClassName($settings,$routeIdSet);
            if ($driverClassInstance !== false) {
                $driverClassInstance->isAvailable() or $driverClassInstance->setup();
                if ($driverClassInstance->isAvailable() != true) {
                    throw new DataRouteInstanceException('Instance.getInstance Failed,Service Not Available.');
                }
                self::$instancePools[$driverKey][$routeIdSet['routeKey']][$routeIdSet['group']] = $driverClassInstance;
                //self::setShutDownHandler();
                return self::$instancePools[$driverKey][$routeIdSet['routeKey']][$routeIdSet['group']];
            }
            throw new DataRouteInstanceException('Instance.getInstance Failed,critical error');
        } else {
            throw new DataRouteInstanceException('driver_not_supported:' . $driverName);
        }
    }


    /**
     * get Data Routed Driver Instance RouteIdSet By routeKey and id vector (data attributes)
     * @param string $driverKey
     * @param string $routeKey
     * @param array $attributes
     * @return bool
     * @throws \Nickfan\AppBox\Common\Exception\DataRouteInstanceException
     */
    public function getRouteInstanceRouteIdSet($driverKey = self::DRIVER_KEY_DEFAULT, $routeKey = DataRouteConf::CONF_KEY_ROOT, $attributes = array()) {
        $driverKey = lcfirst($driverKey);
        $driverName = ucfirst($driverKey);
        return self::$routeConf->getRouteConfKeySetByScript($driverKey, $routeKey, $attributes);
    }

    /**
     * get Data Routed Driver Instance ConfIdSet By routeKey and id vector (data attributes)
     * @param string $driverKey
     * @param string $routeKey
     * @param array $attributes
     * @return bool
     * @throws \Nickfan\AppBox\Common\Exception\DataRouteInstanceException
     */
    public function getRouteInstanceSettings($driverKey = self::DRIVER_KEY_DEFAULT, $routeKey = DataRouteConf::CONF_KEY_ROOT, $attributes = array()) {
        $driverKey = lcfirst($driverKey);
        $driverName = ucfirst($driverKey);
        return self::$routeConf->getRouteConfByScript($driverKey, $routeKey, $attributes);
    }


    /**
     * get Data Routed Conf Subset Keys By routeKey
     * @param string $driverKey
     * @param string $routeKey
     * @return array
     */
    public function getRouteConfKeysByRouteKey($driverKey = self::DRIVER_KEY_DEFAULT, $routeKey = DataRouteConf::CONF_KEY_ROOT){
        $driverKey = lcfirst($driverKey);
        $driverName = ucfirst($driverKey);
        return self::$routeConf->getRouteConfSubKeys($driverKey, $routeKey);
    }

    /**
     * get Data Routed Driver Instance By routeKey and subset(group name)
     * @param string $driverKey
     * @param string $routeKey
     * @param string $subset
     * @return bool
     * @throws \Nickfan\AppBox\Common\Exception\DataRouteInstanceException
     */
    public function getRouteInstanceByConfSubset($driverKey = self::DRIVER_KEY_DEFAULT, $routeKey = DataRouteConf::CONF_KEY_ROOT,$subset=DataRouteConf::CONF_LABEL_INIT){
        $driverKey = lcfirst($driverKey);
        $driverName = ucfirst($driverKey);
        $routeIdSet = array(
            'routeKey'=>$routeKey,
            'group'=>$subset,
        );
        $dataRouteInstance = self::getPoolInstanceByRouteIdSet($driverKey, $routeIdSet);
        if ($dataRouteInstance !== false) {
            return $dataRouteInstance;
        }
        $driverClassName = '\\Nickfan\\AppBox\\Instance\\Drivers\\' . $driverName . 'DataRouteInstanceDriver';

        if (class_exists($driverClassName)) {
            $settings = self::$routeConf->getRouteConfByRouteConfKeySet($driverKey, $routeIdSet);
            $driverClassInstance = new $driverClassName($settings,$routeIdSet);
            if ($driverClassInstance !== false) {
                $driverClassInstance->isAvailable() or $driverClassInstance->setup();
                if ($driverClassInstance->isAvailable() != true) {
                    throw new DataRouteInstanceException('Instance.getInstance Failed,Service Not Available.');
                }
                self::$instancePools[$driverKey][$routeIdSet['routeKey']][$routeIdSet['group']] = $driverClassInstance;
                //self::setShutDownHandler();
                return self::$instancePools[$driverKey][$routeIdSet['routeKey']][$routeIdSet['group']];
            }
            throw new DataRouteInstanceException('Instance.getInstance Failed,critical error');
        } else {
            throw new DataRouteInstanceException('driver_not_supported:' . $driverName);
        }
    }

    /**
     * get Data Routed Conf Subset Settings By routeKey and subset
     * @param string $driverKey
     * @param string $routeKey
     * @return array
     */
    public function getRouteConfSettingsByConfSubset($driverKey = self::DRIVER_KEY_DEFAULT, $routeKey = DataRouteConf::CONF_KEY_ROOT,$subset=DataRouteConf::CONF_LABEL_INIT){
        $driverKey = lcfirst($driverKey);
        $driverName = ucfirst($driverKey);
        $routeIdSet = array(
            'routeKey'=>$routeKey,
            'group'=>$subset,
        );
        return self::$routeConf->getRouteConfByRouteConfKeySet($driverKey, $routeIdSet);
    }

    /**
     * get Single Driver Instance
     * @param string $driverKey
     * @param array $settings
     * @param array $routeIdSet
     * @return mixed
     * @throws \Nickfan\AppBox\Common\Exception\DataRouteInstanceException
     */
    public function getDriverInstance($driverKey=self::DRIVER_KEY_DEFAULT,$settings=array(),$routeIdSet = array()){
        $driverKey = lcfirst($driverKey);
        $driverName = ucfirst($driverKey);
        $driverClassName = '\\Nickfan\\AppBox\\Instance\\Drivers\\' . $driverName . 'DataRouteInstanceDriver';

        if (class_exists($driverClassName)) {
            $driverClassInstance = new $driverClassName($settings,$routeIdSet);
            if ($driverClassInstance !== false) {
                $driverClassInstance->isAvailable() or $driverClassInstance->setup();
                if ($driverClassInstance->isAvailable() != true) {
                    throw new DataRouteInstanceException('Instance.getInstance Failed,Service Not Available.');
                }
                register_shutdown_function(array($driverClassInstance, 'close'));
                return $driverClassInstance;
            }
            throw new DataRouteInstanceException('Instance.getInstance Failed,critical error');
        } else {
            throw new DataRouteInstanceException('driver_not_supported:' . $driverName);
        }
    }


    /**
     * get connected poolInstanceRouteIdLabels
     * @param string $driverKey
     * @return array
     */
    protected static function getPoolInstanceRouteIdLabels($driverKey = self::DRIVER_KEY_DEFAULT) {
        $retKeys = array();
        if (!empty($driverKey) && isset(self::$instancePools[$driverKey])) {
            $retKeys = array_keys(self::$instancePools[$driverKey]);
        }
        return $retKeys;
    }


    /**
     * get connected poolInstance By routeIdSet
     * @param string $driverKey
     * @param array $routeIdSet
     * @return bool
     */
    protected static function getPoolInstanceByRouteIdSet($driverKey = self::DRIVER_KEY_DEFAULT, $routeIdSet = array()) {
        if (!isset(self::$instancePools[$driverKey])) {
            self::$instancePools[$driverKey] = array();
            return false;
        }
        if (!isset(self::$instancePools[$driverKey][$routeIdSet['routeKey']])) {
            self::$instancePools[$driverKey][$routeIdSet['routeKey']] = array();
            return false;
        }
        if (!isset(self::$instancePools[$driverKey][$routeIdSet['routeKey']][$routeIdSet['group']])) {
            return false;
        } else {
            if (self::$instancePools[$driverKey][$routeIdSet['routeKey']][$routeIdSet['group']] !== false) {
                self::$instancePools[$driverKey][$routeIdSet['routeKey']][$routeIdSet['group']]->isAvailable(
                ) or self::$instancePools[$driverKey][$routeIdSet['routeKey']][$routeIdSet['group']]->setup();
                if (self::$instancePools[$driverKey][$routeIdSet['routeKey']][$routeIdSet['group']]->isAvailable(
                    ) != true
                ) {
                    return false;
                    //throw new DataRouteInstanceException(_('Instance.getInstance Failed,Service Not Available.'),500);
                } else {
                    return self::$instancePools[$driverKey][$routeIdSet['routeKey']][$routeIdSet['group']];
                }
            }
        }
        return false;
    }

    /**
     * close all instance connection
     * @return void
     */
    public static function close() {
        if (!empty(self::$instancePools)) {
            foreach (self::$instancePools as $driverKey => $driverRouteInstancePools) {
                if (!empty($driverRouteInstancePools)) {
                    foreach ($driverRouteInstancePools as $routeKey => $groupDataRouteInstances) {
                        if (!empty($groupDataRouteInstances)) {
                            foreach ($groupDataRouteInstances as $group => $dataRouteInstance) {
                                if ($dataRouteInstance) {
                                    $dataRouteInstance->close();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function __destruct() {
        self::close();
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