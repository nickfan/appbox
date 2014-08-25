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
use Nickfan\AppBox\DataObject\BaseDataObject;
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
    protected static $ttlCache = 0;        // 缓存ttl
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
            $driverInstance = self::initDriverInstanceByName($instanceDriverName);
            $driverInstance->setRouteKey($this->getObjectName());
            $this->instanceMap[$instanceDriverName] = $driverInstance;
        }
    }

    /**
     * 根据驱动名称初始化service驱动实例
     * @param $instanceDriverName
     * @return mixed
     * @throws \Nickfan\AppBox\Common\Exception\RuntimeException
     */
    protected static function initDriverInstanceByName($instanceDriverName){
        $driverClassKey = ucfirst($instanceDriverName);
        $driverClassName = '\\Nickfan\\AppBox\\Service\\Drivers\\' . $driverClassKey . 'DataRouteServiceDriver';
        if (class_exists($driverClassName)) {
            $driverInstance = call_user_func_array(array($driverClassName, 'factory'), array(self::$routeInstance));
            if ($driverInstance) {
                return $driverInstance;
            } else {
                throw new RuntimeException(' driver instance init failed :' . $driverClassName);
            }
        } else {
            throw new RuntimeException('request Driver not found :' . $driverClassName);
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
     * 检测是否需要升级数据结构
     * @param $reqData
     * @param string $objectLabel
     * @param string $namespace
     * @return bool
     */
    protected function _checkNeedUpgradeObjectByLabel($reqData,$objectLabel='', $namespace = ''){
        $retObject = false;
        //数据对象需要升级

        $namespace == '' && $namespace = $this->getDefaultNamespace();
        if(!empty($namespace)){
            if(strlen($namespace)!=(strrpos($namespace,'\\')+1)){
                $namespace.='\\';
            }
        }
        $objectClassName = $namespace . ucfirst($objectLabel) . 'DataObject';

        if(array_key_exists(BaseDataObject::DO_VERSION_PROP, $reqData) && $reqData[BaseDataObject::DO_VERSION_PROP]!=$objectClassName::DO_VERSION){
            $retObject=true;
        }
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


    /**
     * 消息队列处理任务
     * @param $callback
     * @param $routeInstance
     * @param array $option
     * @return mixed|null
     * @throws Exception
     */
    public static function mqProcessTaskData($callback,$taskLabel='_default_',$option=array(),$vendorInstance=null){
        $option+=array(
            'mqInstance'=>AppConstants::INSTANCE_MQ_DRIVER_BEANSTALK, //队列服务实例
            'mqPrefix'=>static::$prefixMq,
            'decode'=>true,
            'delete'=>true,
            'release'=>false,
            'delay'=>10,
            'sleep'=>1,
            'priority'=>1024,
            'timeout'=>3,
            'dataRouteInstance'=>null,
        );


        if(!is_callable($callback)){
            throw new RuntimeException('callback error: is not callable');
        }

        if(is_null($vendorInstance)){
            if(is_null($option['dataRouteInstance'])){
                $option['dataRouteInstance'] = static::$routeInstance;
            }
        }

        $retData = null;

        $driverInstance = self::initDriverInstanceByName($option['mqInstance']);
        list($vendorInstance, $option) = $driverInstance->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $taskLabel,)
        );

        switch($option['mqInstance']){
            case AppConstants::INSTANCE_MQ_DRIVER_REDIS:
                // redis 这边需要默认socket 超时不限
                //@ini_set('default_socket_timeout', -1);
                !isset($option['options']['type']) && $option['options']['type'] = 'list';
                if($option['options']['type']!='list'){
                    $retData = $vendorInstance->subscribe($option['mqPrefix'].$taskLabel,$callback);
                }else{
                    $job = $vendorInstance->blPop($option['mqPrefix'].$taskLabel,$option['timeout']);
                    if($option['decode']==true){
                        $arg= Util::dataunpack($job);
                    }else{
                        $arg= $job;
                    }
                    $retData = call_user_func($callback, $arg);
                    //sleep($option['sleep']);
                }
                break;
            case AppConstants::INSTANCE_MQ_DRIVER_BEANSTALK:
            default:
                try {
                    //$tubeStats = $vendorInstance->statsTube($option['mqPrefix'].$taskLabel);
                    //if($tubeStats){
                    $vendorInstance->watchOnly($option['mqPrefix'].$taskLabel);
                    $job = $vendorInstance->reserve($option['timeout']);
                    //var_dump($job);
                    if($job){
                        if($option['decode']==true){
                            $arg= Util::dataunpack($job->getData());
                        }else{
                            $arg= $job->getData();
                        }
                        $retData = call_user_func($callback, $arg);
                        if(!$retData){
                            if($option['release']==true){
                                $vendorInstance->release($job,$option['priority'],$option['delay']);
                            }
                            throw new RuntimeException('process data error');
                        }
                        if($option['delete']==true){
                            $vendorInstance->delete($job);
                        }
                    }else{
                        $arg = $job;
                    }
                    //}
                } catch (\Exception $ex) {
                    throw $ex;
                }
                //sleep($option['sleep']);
                break;
        }
        return $retData;
    }



    /**
     * 根据数据类型标识、对象ID获取对应对象数据
     * @param @param string $objectLabel
     * @param int $requestId
     * @param array $option
     * @throws MyRuntimeException
     * @return array|null
     */
    public function _getStructDataById($objectLabel='',$requestId=0,$option=array()){
        $returnData = null;
        $option+=array(
            'idLabel'=>'id',    // 自定义idlabel （字段名）
            'dbInstance'=>$this->getInstanceTypeDriverName(AppConstants::INSTANCE_TYPE_DB),	// 数据库实例
            'dbPrefix'=>static::$prefixDb,
            'dbOption'=>array(
//                'collectionName' => null,  //指定特定的集合
//                'dbName' => null,  //指定特定的库名
//                'options'=>array(),
            ),// 数据库选项
            'cacheInstance'=>$this->getInstanceTypeDriverName(AppConstants::INSTANCE_TYPE_CACHE),	// cache实例类型
            'useCache'=>true,		// 从cache读取
            'setCache'=>true,		// 写回cache
            'delCache'=>false,		// 清除cache
            'cacheOption'=>array(),// 缓存路由选项
            'cachePrefix'=>static::$prefixCache,
            'cacheTtl'=>static::$ttlCache,

        );
        // mongo对类型敏感，假设所有的ID是数字类型时，强制类型转换
        is_numeric($requestId) && $requestId = intval($requestId);


        if(empty($requestId)){
            return $this->getDataObjectTemplateByLabel($objectLabel);
        }


        $needUpgrade = true;
        $dataFromCache = false;
        $optionCache = array(
            'routeKey'=>$objectLabel,
        );
        !empty($option['cacheOption']) && $optionCache = array_merge($optionCache,$option['cacheOption']);

        if($option['useCache']==true){
            // 读取缓存
            switch($option['cacheInstance']){
                case AppConstants::INSTANCE_CACHE_DRIVER_MEMCACHE:
                    if($this->getInstanceTypeDriverName(AppConstants::INSTANCE_TYPE_CACHE)==AppConstants::INSTANCE_CACHE_DRIVER_MEMCACHE){
                        !isset($cacheDriverInstance) && $cacheDriverInstance = $this->getSetInstanceTypeDriverInstance(AppConstants::INSTANCE_TYPE_CACHE);
                    }else{
                        !isset($cacheDriverInstance) && $cacheDriverInstance = self::initDriverInstanceByName(AppConstants::INSTANCE_CACHE_DRIVER_MEMCACHE);
                        //$driverInstance->setRouteKey($this->getObjectName());
                        $cacheDriverInstance->setRouteKey($objectLabel);
                    }
                    $objectCacheKey = $option['cachePrefix'].$objectLabel.AppConstants::KEYSEP.$requestId;
                    $returnData = $cacheDriverInstance->get($objectCacheKey,$optionCache);
                    break;
                case AppConstants::INSTANCE_CACHE_DRIVER_REDIS:
                default:
                    if($this->getInstanceTypeDriverName(AppConstants::INSTANCE_TYPE_CACHE)==AppConstants::INSTANCE_CACHE_DRIVER_REDIS){
                        !isset($cacheDriverInstance) && $cacheDriverInstance = $this->getSetInstanceTypeDriverInstance(AppConstants::INSTANCE_TYPE_CACHE);
                    }else{
                        !isset($cacheDriverInstance) && $cacheDriverInstance = self::initDriverInstanceByName(AppConstants::INSTANCE_CACHE_DRIVER_REDIS);
                        //$driverInstance->setRouteKey($this->getObjectName());
                        $cacheDriverInstance->setRouteKey($objectLabel);
                    }
                    $objectCacheKey = $option['cachePrefix'].$objectLabel.AppConstants::KEYSEP.$requestId;
                    $returnData = $cacheDriverInstance->get($objectCacheKey,$optionCache);
                    break;
            }
            $returnData !== false && $returnData = Util::dataunpack($returnData);
            if(!empty($returnData)){
                $needUpgrade = $this->_checkNeedUpgradeObjectByLabel($returnData,$objectLabel);
                if($needUpgrade==false){
                    $dataFromCache = true;
                }
            }
        }
        if(empty($returnData) || $needUpgrade==true){
            $dataFromCache = false;
            $optionDb = array(
                'routeKey'=>$objectLabel,
            );
            !empty($option['dbOption']) && $optionDb = array_merge($optionDb,$option['dbOption']);

            switch($option['dbInstance']){
                case AppConstants::INSTANCE_DB_DRIVER_MYSQL:
                case AppConstants::INSTANCE_DB_DRIVER_MSSQL:
                    if(in_array($this->getInstanceTypeDriverName(AppConstants::INSTANCE_TYPE_DB),array(AppConstants::INSTANCE_DB_DRIVER_MYSQL,AppConstants::INSTANCE_DB_DRIVER_MSSQL))){
                        !isset($cacheDriverInstance) && $dbDriverInstance = $this->getSetInstanceTypeDriverInstance(AppConstants::INSTANCE_TYPE_DB);
                    }else{
                        !isset($cacheDriverInstance) && $dbDriverInstance = self::initDriverInstanceByName($option['dbInstance']);
                        //$dbDriverInstance->setRouteKey($this->getObjectName());
                        $dbDriverInstance->setRouteKey($objectLabel);
                    }
                    $queryStruct=array(
                        'querySchema'=>array($objectLabel, ),
                        'conditionKey'=>array(
                            'id'=>$requestId,
                        ),
                    );
                    $returnData = $dbDriverInstance->queryRow($queryStruct,$optionDb);
                    break;
                case AppConstants::INSTANCE_DB_DRIVER_REDIS:
                    if($this->getInstanceTypeDriverName(AppConstants::INSTANCE_TYPE_DB)==AppConstants::INSTANCE_DB_DRIVER_REDIS){
                        !isset($cacheDriverInstance) && $dbDriverInstance = $this->getSetInstanceTypeDriverInstance(AppConstants::INSTANCE_TYPE_DB);
                    }else{
                        !isset($cacheDriverInstance) && $dbDriverInstance = self::initDriverInstanceByName(AppConstants::INSTANCE_DB_DRIVER_REDIS);
                        //$dbDriverInstance->setRouteKey($this->getObjectName());
                        $dbDriverInstance->setRouteKey($objectLabel);
                    }
                    $objectDbKey = $option['dbPrefix'].$objectLabel.AppConstants::KEYSEP.$requestId;
                    $returnData = $dbDriverInstance->get($objectDbKey,$optionDb);
                    $returnData !== false && $returnData = Util::dataunpack($returnData);
                    break;
                case AppConstants::INSTANCE_DB_DRIVER_MONGODB:
                default:
                    $dbName = (isset($optionDb['dbName']) && !empty($optionDb['dbName']))?$optionDb['dbName']:lcfirst($objectLabel);
                    $collectionName = (isset($optionDb['collectionName']) && !empty($optionDb['collectionName']))?$optionDb['collectionName']:lcfirst($objectLabel);
                    if($this->getInstanceTypeDriverName(AppConstants::INSTANCE_TYPE_DB)==AppConstants::INSTANCE_DB_DRIVER_MONGODB){
                        $dbDriverInstance = $this->getSetInstanceTypeDriverInstance(AppConstants::INSTANCE_TYPE_DB);
                    }else{
                        $dbDriverInstance = self::initDriverInstanceByName(AppConstants::INSTANCE_DB_DRIVER_MONGODB);
                        $dbDriverInstance->__init(array('routeKey'=>$objectLabel,'dbName'=>$dbName));
                    }
                    $optionReq = $optionDb;
                    $optionReq['idLabel'] = $option['idLabel'];
                    $returnData = $dbDriverInstance->read(array($option['idLabel']=>$requestId),$optionReq);
                    break;
            }

            if(!empty($returnData)){
                //如果获取的数据对象需要升级
                //$returnData =  $this->_checkUpgradeObjectByLabel($returnData,$objectLabel,$optionData);
                if($option['setCache']==true){
                    // 写入缓存
                    switch($option['cacheInstance']){
                        case AppConstants::INSTANCE_CACHE_DRIVER_MEMCACHE:
                            if($this->getInstanceTypeDriverName(AppConstants::INSTANCE_TYPE_CACHE)==AppConstants::INSTANCE_CACHE_DRIVER_MEMCACHE){
                                !isset($cacheDriverInstance) && $cacheDriverInstance = $this->getSetInstanceTypeDriverInstance(AppConstants::INSTANCE_TYPE_CACHE);
                            }else{
                                !isset($cacheDriverInstance) && $cacheDriverInstance = self::initDriverInstanceByName(AppConstants::INSTANCE_CACHE_DRIVER_MEMCACHE);
                                //$driverInstance->setRouteKey($this->getObjectName());
                                $cacheDriverInstance->setRouteKey($objectLabel);
                            }
                            $objectCacheKey = $option['cachePrefix'].$objectLabel.AppConstants::KEYSEP.$requestId;
                            $cacheDriverInstance->set($objectCacheKey,Util::datapack($returnData),$option['cacheTtl'],$optionCache);
                            break;
                        case AppConstants::INSTANCE_CACHE_DRIVER_REDIS:
                        default:
                            if($this->getInstanceTypeDriverName(AppConstants::INSTANCE_TYPE_CACHE)==AppConstants::INSTANCE_CACHE_DRIVER_REDIS){
                                !isset($cacheDriverInstance) && $cacheDriverInstance = $this->getSetInstanceTypeDriverInstance(AppConstants::INSTANCE_TYPE_CACHE);
                            }else{
                                !isset($cacheDriverInstance) && $cacheDriverInstance = self::initDriverInstanceByName(AppConstants::INSTANCE_CACHE_DRIVER_REDIS);
                                //$driverInstance->setRouteKey($this->getObjectName());
                                $cacheDriverInstance->setRouteKey($objectLabel);
                            }
                            $objectCacheKey = $option['cachePrefix'].$objectLabel.AppConstants::KEYSEP.$requestId;
                            $cacheDriverInstance->setex($objectCacheKey,$option['cacheTtl'],Util::datapack($returnData),$optionCache);
                            break;
                    }
                }
            }
        }
        if($option['delCache']!==false){
            //删除cache
            switch($option['cacheInstance']){
                case AppConstants::INSTANCE_CACHE_DRIVER_MEMCACHE:
                    if($this->getInstanceTypeDriverName(AppConstants::INSTANCE_TYPE_CACHE)==AppConstants::INSTANCE_CACHE_DRIVER_MEMCACHE){
                        !isset($cacheDriverInstance) && $cacheDriverInstance = $this->getSetInstanceTypeDriverInstance(AppConstants::INSTANCE_TYPE_CACHE);
                    }else{
                        !isset($cacheDriverInstance) && $cacheDriverInstance = self::initDriverInstanceByName(AppConstants::INSTANCE_CACHE_DRIVER_MEMCACHE);
                        //$driverInstance->setRouteKey($this->getObjectName());
                        $cacheDriverInstance->setRouteKey($objectLabel);
                    }
                    $objectCacheKey = $option['cachePrefix'].$objectLabel.AppConstants::KEYSEP.$requestId;
                    $cacheDriverInstance->delete($objectCacheKey,0,$optionCache);
                    break;
                case AppConstants::INSTANCE_CACHE_DRIVER_REDIS:
                default:
                    if($this->getInstanceTypeDriverName(AppConstants::INSTANCE_TYPE_CACHE)==AppConstants::INSTANCE_CACHE_DRIVER_REDIS){
                        !isset($cacheDriverInstance) && $cacheDriverInstance = $this->getSetInstanceTypeDriverInstance(AppConstants::INSTANCE_TYPE_CACHE);
                    }else{
                        !isset($cacheDriverInstance) && $cacheDriverInstance = self::initDriverInstanceByName(AppConstants::INSTANCE_CACHE_DRIVER_REDIS);
                        //$driverInstance->setRouteKey($this->getObjectName());
                        $cacheDriverInstance->setRouteKey($objectLabel);
                    }
                    $objectCacheKey = $option['cachePrefix'].$objectLabel.AppConstants::KEYSEP.$requestId;
                    $cacheDriverInstance->delete($objectCacheKey,$optionCache);
                    break;
            }
        }
        return $returnData;
    }

}
