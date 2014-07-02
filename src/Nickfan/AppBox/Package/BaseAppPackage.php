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
use Nickfan\AppBox\Instance\DataRouteInstance;

abstract class BaseAppPackage {

    protected static $instances = array();
    protected static $routeInstance = null;

    protected $objectName = '';    // ClassBase

    protected $instanceTypeMap = array(
        AppConstants::INSTANCE_TYPE_CACHE => AppConstants::INSTANCE_CACHE_TYPE_NONE,        // 缓存
        AppConstants::INSTANCE_TYPE_SEQ => AppConstants::INSTANCE_SEQ_TYPE_NONE,            // 序列生成器
        AppConstants::INSTANCE_TYPE_DB => AppConstants::INSTANCE_DB_TYPE_NONE,              // 数据库
        AppConstants::INSTANCE_TYPE_IDX => AppConstants::INSTANCE_IDX_TYPE_NONE,            // 索引（全文检索）
        AppConstants::INSTANCE_TYPE_MQ => AppConstants::INSTANCE_MQ_TYPE_NONE,              // 消息队列
        AppConstants::INSTANCE_TYPE_FS => AppConstants::INSTANCE_FS_TYPE_NONE,              // 文件存储
        AppConstants::INSTANCE_TYPE_RPC => AppConstants::INSTANCE_RPC_TYPE_NONE,            // 远程调用
        AppConstants::INSTANCE_TYPE_DC => AppConstants::INSTANCE_DC_TYPE_NONE,              // 分布式计算
    );

    protected $instanceMap = array(
        /*
         * eg:
         * 'cache' =>  instance of \Nickfan\AppBox\Service\Drivers\RedisDataRouteServiceDriver
         */
    );

    public static function getInstance(DataRouteInstance $instDataRouteInstance, $objectName = ""){
        $className = get_called_class();
        if($objectName==''){
            $objectName = substr($className, 0, -7);
        }
        if(!isset(static::$instances[$objectName])){
            static::$instances[$objectName] = new $className($instDataRouteInstance,$objectName);
        }
        return static::$instances[$objectName];
    }
    protected function __construct(DataRouteInstance $instDataRouteInstance = NULL, $objectName = ""){
        if($objectName == "")
        {
            $this->objectName = substr(get_class($this), 0, -7);// 0 -8 : _Model
        }
        else
        {
            $this->objectName = $objectName;
        }
        if(is_null($instDataRouteInstance)){
            throw new RuntimeException('Package.DataRouteInstance is null :'.get_class($this));
        }else{
            self::$routeInstance = $instDataRouteInstance;
        }
        return $this;
    }

    public function setObjectName($objectName){
        $this->objectName = $objectName;
    }
    public function getObjectName(){
        return $this->objectName;
    }
    protected function getInstanceTypeDriverInstance($instanceType){
        if(isset($this->instanceTypeMap[$instanceType])){
            if($this->instanceTypeMap[$instanceType]==''){
                throw new RuntimeException('instance type driver none :'.$instanceType);
            }else{
                return $this->getDriverInstance($this->instanceTypeMap[$instanceType]);
            }
        }else{
            throw new RuntimeException('unknown instance type :'.$instanceType);
        }
    }

    protected function setInstanceTypeDriver($instanceType,$instanceDriverName=''){
        if(isset($this->instanceTypeMap[$instanceType]) && $this->instanceTypeMap[$instanceType]!=$instanceDriverName){
            $instanceDriverName!='' && $this->setDriverInstance($instanceDriverName);
            $this->instanceTypeMap[$instanceType] = $instanceDriverName;
        }
    }

    protected function setDriverInstance($instanceDriverName){
        if(!isset($this->instanceMap[$instanceDriverName]) || is_null($this->instanceMap[$instanceDriverName])){
            $driverClassKey = ucfirst($instanceDriverName);
            $driverClassName = '\\Nickfan\\AppBox\\Service\\Drivers\\'.$driverClassKey.'DataRouteServiceDriver';
            if(class_exists($driverClassName)){
                $driverInstance = call_user_func_array(array($driverClassName,'factory'),array(self::$routeInstance));
                if($driverInstance){
                    $driverInstance->setRouteKey($this->getObjectName());
                    $this->instanceMap[$instanceDriverName] = $driverInstance;
                }else{
                    throw new RuntimeException(' driver instance init failed :'.$driverClassName);
                }
            }else{
                throw new RuntimeException('request Driver not found :'.$driverClassName);
            }
        }
    }
    protected function getDriverInstance($instanceDriverName){
        if(!isset($this->instanceMap[$instanceDriverName]) || is_null($this->instanceMap[$instanceDriverName])){
            $this->setDriverInstance($instanceDriverName);
        }
        return $this->instanceMap[$instanceDriverName];
    }
} 