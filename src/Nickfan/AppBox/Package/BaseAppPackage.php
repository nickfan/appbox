<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-07-02 11:00
 *
 */


namespace Nickfan\AppBox\Package;

use Nickfan\AppBox\Common\AppConstants;
use Nickfan\AppBox\Common\Exception\RuntimeException;
use Nickfan\AppBox\Instance\DataRouteInstanceInterface;
use Nickfan\AppBox\Support\Util;

abstract class BaseAppPackage implements PackageInterface {

    protected static $instances = array();
    protected static $routeInstance = null;

    protected $objectName = ''; // ClassBase

    protected $defaultNameSpace = '';
    protected $instanceTypeMap = array(
        AppConstants::INSTANCE_TYPE_CACHE => AppConstants::INSTANCE_CACHE_DRIVER_NONE, // 缓存
        AppConstants::INSTANCE_TYPE_SEQ => AppConstants::INSTANCE_SEQ_DRIVER_NONE, // 序列生成器
        AppConstants::INSTANCE_TYPE_DB => AppConstants::INSTANCE_DB_DRIVER_NONE, // 数据库
        AppConstants::INSTANCE_TYPE_IDX => AppConstants::INSTANCE_IDX_DRIVER_NONE, // 索引（全文检索）
        AppConstants::INSTANCE_TYPE_MQ => AppConstants::INSTANCE_MQ_DRIVER_NONE, // 消息队列
        AppConstants::INSTANCE_TYPE_FS => AppConstants::INSTANCE_FS_DRIVER_NONE, // 文件存储
        AppConstants::INSTANCE_TYPE_RPC => AppConstants::INSTANCE_RPC_DRIVER_NONE, // 远程调用
        AppConstants::INSTANCE_TYPE_DC => AppConstants::INSTANCE_DC_DRIVER_NONE, // 分布式计算
    );

    protected $instanceMap = array(/*
         * eg:
         * 'cache' =>  instance of \Nickfan\AppBox\Service\Drivers\RedisDataRouteServiceDriver
         */
    );

    protected static $prefixCache = 'apch_';        // 缓存前缀
    protected static $prefixDb = 'apdt_';           // 数据库表前缀
    protected static $prefixMq = '';           // 队列前缀

    public static function parseClassNameObjectName() {
        $className = get_called_class();
        $shortClassName = substr(
            $className,
            strrpos($className, '\\') + 1,
            -7
        ); // -7 = strlen(Package)
        if (!empty($shortClassName)) {
            $objectName = lcfirst($shortClassName);
        } else {
            $objectName = '';
        }
        return array($className, $shortClassName, $objectName);
    }

    public static function getInstance(DataRouteInstanceInterface $instDataRouteInstance, $objectName = "") {
        if ($objectName == '') {
            list($className, $shortClassName, $objectName) = self::parseClassNameObjectName();
        }
        if (!isset(static::$instances[$objectName])) {
            static::$instances[$objectName] = new $className($instDataRouteInstance, $objectName);
        }
        return static::$instances[$objectName];
    }

    protected function __construct(DataRouteInstanceInterface $instDataRouteInstance = null, $objectName = "") {
        if ($objectName == "") {
            list($className, $shortClassName, $objectName) = self::parseClassNameObjectName();
        } else {
            $this->objectName = $objectName;
        }
        if (is_null($instDataRouteInstance)) {
            throw new RuntimeException('Package.DataRouteInstance is null :' . get_class($this));
        } else {
            self::$routeInstance = $instDataRouteInstance;
        }
        $this->setDefaultNamespace(__NAMESPACE__);
        return $this;
    }

    public function setObjectName($objectName) {
        $this->objectName = $objectName;
    }

    public function getObjectName() {
        return $this->objectName;
    }

    public function setDefaultNamespace($namespace = '') {
        if ($this->missingLeadingSlash($namespace)) {
            $namespace = '\\' . $namespace;
        }
        $this->defaultNameSpace = $namespace;
    }

    public function getDefaultNamespace() {
        return $this->defaultNameSpace;
    }

    /**
     * Determine if the given abstract has a leading slash.
     *
     * @param  string $abstract
     * @return bool
     */
    protected function missingLeadingSlash($namespace) {
        return is_string($namespace) && strpos($namespace, '\\') !== 0;
    }

    public function getDataRouteInstance() {
        return self::$routeInstance;
    }

    public function setDataRouteInstance(DataRouteInstanceInterface $instDataRouteInstance) {
        self::$routeInstance = $instDataRouteInstance;
    }

    /**
     * 获取实例类别下的对应驱动实例
     * @param $instanceType
     * @return mixed
     * @throws \Nickfan\AppBox\Common\Exception\RuntimeException
     */
    protected function getInstanceTypeDriverInstance($instanceType) {
        if (isset($this->instanceTypeMap[$instanceType])) {
            if ($this->instanceTypeMap[$instanceType] == '') {
                throw new RuntimeException('instance type driver none :' . $instanceType);
            } else {
                return $this->getDriverInstance($this->instanceTypeMap[$instanceType]);
            }
        } else {
            throw new RuntimeException('unknown instance type :' . $instanceType);
        }
    }

    protected function getInstanceTypeDriverName($instanceType){
        if (isset($this->instanceTypeMap[$instanceType])) {
            return $this->instanceTypeMap[$instanceType];
        } else {
            throw new RuntimeException('unknown instance type :' . $instanceType);
        }
    }
    /**
     * 获取实例类别下的对应驱动实例 可选设定新驱动名称（比如cache换用redis/memcache）
     * @param $instanceType
     * @param string $instanceDriverName
     * @return mixed
     * @throws \Nickfan\AppBox\Common\Exception\RuntimeException
     */
    protected function getSetInstanceTypeDriverInstance($instanceType, $instanceDriverName = '') {
        if (isset($this->instanceTypeMap[$instanceType])) {
            if ($this->instanceTypeMap[$instanceType] == '') {
                if ($instanceDriverName == '') {
                    throw new RuntimeException('instance type driver none :' . $instanceType);
                } else {
                    $driverInstance = $this->getDriverInstance($instanceDriverName);
                    if ($driverInstance) {
                        $this->instanceTypeMap[$instanceType] = $instanceDriverName;
                    }
                    return $driverInstance;
                }
            } else {
                if ($instanceDriverName != '') {
                    if ($this->instanceTypeMap[$instanceType] != $instanceDriverName) {
                        $driverInstance = $this->getDriverInstance($instanceDriverName);
                        if ($driverInstance) {
                            $this->instanceTypeMap[$instanceType] = $instanceDriverName;
                        }
                        return $driverInstance;
                    } else {
                        return $this->getDriverInstance($this->instanceTypeMap[$instanceType]);
                    }
                } else {
                    return $this->getDriverInstance($this->instanceTypeMap[$instanceType]);
                }

            }
        } else {
            throw new RuntimeException('unknown instance type :' . $instanceType);
        }
    }

    /**
     * 设置实例类别下的对应驱动实例
     * @param $instanceType
     * @param string $instanceDriverName
     */
    protected function setInstanceTypeDriver($instanceType, $instanceDriverName = '') {
        if (isset($this->instanceTypeMap[$instanceType]) && $this->instanceTypeMap[$instanceType] != $instanceDriverName) {
            $instanceDriverName != '' && $this->setDriverInstance($instanceDriverName);
            $this->instanceTypeMap[$instanceType] = $instanceDriverName;
        }
    }

    /**
     * 设置驱动实例根据驱动名称
     * @param $instanceDriverName
     * @throws \Nickfan\AppBox\Common\Exception\RuntimeException
     */
    protected function setDriverInstance($instanceDriverName) {
        if (!isset($this->instanceMap[$instanceDriverName]) || is_null($this->instanceMap[$instanceDriverName])) {
            $driverClassKey = ucfirst($instanceDriverName);
            $driverClassName = '\\Nickfan\\AppBox\\Service\\Drivers\\' . $driverClassKey . 'DataRouteServiceDriver';
            if (class_exists($driverClassName)) {
                $driverInstance = call_user_func_array(array($driverClassName, 'factory'), array(self::$routeInstance));
                if ($driverInstance) {
                    $driverInstance->setRouteKey($this->getObjectName());
                    $this->instanceMap[$instanceDriverName] = $driverInstance;
                } else {
                    throw new RuntimeException(' driver instance init failed :' . $driverClassName);
                }
            } else {
                throw new RuntimeException('request Driver not found :' . $driverClassName);
            }
        }
    }

    /**
     * 获取驱动实例根据驱动名称
     * @param $instanceDriverName
     * @return mixed
     */
    public function getDriverInstance($instanceDriverName) {
        if (!isset($this->instanceMap[$instanceDriverName]) || is_null($this->instanceMap[$instanceDriverName])) {
            $this->setDriverInstance($instanceDriverName);
        }
        return $this->instanceMap[$instanceDriverName];
    }


    /**
     * 获取单体对象模板
     * @param string $objectLabel
     * @param array $defprops
     * @param string $namespace
     * @return array
     */
    public function getDataObjectTemplateByLabel($objectLabel = '', $defprops = array(), $namespace = '') {
        $retObject = array();
        $namespace == '' && $namespace = $this->getDefaultNamespace();
        if(!empty($namespace)){
            if(strlen($namespace)!=(strrpos($namespace,'\\')+1)){
                $namespace.='\\';
            }
        }
        $objectClassName = $namespace . ucfirst($objectLabel) . 'DataObject';
        if (!empty($defprops)) {
            $doInstance = new $objectClassName($defprops);
        } else {
            $doInstance = new $objectClassName();
        }
        $retObject = $doInstance->toArray();
        return $retObject;
    }



    /**
     * 消息队列发布任务
     * @param array $data
     * @param string $taskLabel
     * @param array $option
     * @return mixed|null|void
     */
    public function mqTaskPush($data=array(),$taskLabel='_default_',$option=array(),$vendorInstance=null){
        $option+=array(
            'mqInstance'=>$this->getInstanceTypeDriverName(AppConstants::INSTANCE_TYPE_MQ), //队列服务实例
            'mqPrefix'=>static::$prefixMq,
            'options'=>array(),
            'encode'=>true,
            'dataRouteInstance'=>$this->getDataRouteInstance(),
        );
        $retStatus = null;
        $message = $option['encode']==true?Util::datapack($data):$data;
        $driverInstance = $this->getSetInstanceTypeDriverInstance(AppConstants::INSTANCE_TYPE_MQ);
        list($vendorInstance, $option) = $driverInstance->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $taskLabel,)
        );

        switch($option['mqInstance']){
            case AppConstants::INSTANCE_MQ_DRIVER_REDIS:
                !isset($option['options']['type']) && $option['options']['type'] = 'list';
                if($option['options']['type']!='list'){
                    $retStatus = $vendorInstance->publish($option['mqPrefix'].$taskLabel,$message);
                }else{
                    $retStatus = $vendorInstance->lPush($option['mqPrefix'].$taskLabel,$message);
                }
                break;
            case AppConstants::INSTANCE_MQ_DRIVER_BEANSTALK:
            default:
                $retStatus = $vendorInstance->useTube($option['mqPrefix'].$taskLabel)->put($message);
                break;
        }

        return $retStatus;
    }
}
