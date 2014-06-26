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
    public static function getInstance(DataRouteConf $routeConf) {
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
    protected function __construct(DataRouteConf $routeConf) {
        self::$routeConf = $routeConf;
    }

    public static function setShutDownHandler() {
        if (self::$setShutdownHandler == true) {
            register_shutdown_function(array('\\Nickfan\\AppBox\\Instance\\DataRouteInstance', 'close'));
        }
    }

    public function getRouteInstance($driverKey = 'cfg', $routeKey = 'root', $attributes = array()) {
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
            $driverClassInstance = new $driverClassName($routeIdSet, $settings);
            if ($driverClassInstance !== false) {
                $driverClassInstance->isAvailable() or $driverClassInstance->setup();
                if ($driverClassInstance->isAvailable() != true) {
                    throw new DataRouteInstanceException('Instance.getInstance Failed,Service Not Available.');
                }
                self::$instancePools[$driverKey][$routeIdSet['routeKey']][$routeIdSet['group']] = $driverClassInstance;
                self::setShutDownHandler();
                return self::$instancePools[$driverKey][$routeIdSet['routeKey']][$routeIdSet['group']];
            }
            throw new DataRouteInstanceException('Instance.getInstance Failed,critical error');
        } else {
            throw new DataRouteInstanceException('driver_not_supported:' . $driverName);
        }
    }

    public static function getPoolInstanceRouteIdLabels($driverKey = null) {
        $retKeys = array();
        if (!empty($driverKey) && isset(self::$instancePools[$driverKey])) {
            $retKeys = array_keys(self::$instancePools[$driverKey]);
        }
        return $retKeys;
    }

    public static function getPoolInstanceByRouteIdSet($driverKey = null, $routeIdSet = array()) {
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

    public static function close() {
        //debug_print_backtrace();
        //exit('serv close');
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
            //print(PHP_EOL.'<br/><pre>'.PHP_EOL.var_export( self::$instancePools,TRUE).PHP_EOL.'</pre><br/>'.PHP_EOL);exit;
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