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

use Nickfan\AppBox\Common\BoxConstants;
use Nickfan\AppBox\Common\Exception\RuntimeException;
use Nickfan\AppBox\BoxObject\BoxObject;
use Nickfan\AppBox\Instance\BoxRouteInstanceInterface;
use Nickfan\AppBox\Service\Drivers\DbBoxRouteServiceDriver;
use Nickfan\AppBox\Service\Drivers\MongoBoxRouteServiceDriver;
use Nickfan\AppBox\Support\BoxUtil;

abstract class BoxBasePackage implements BoxPackageInterface {

    protected static $instances = array();
    protected static $routeInstance = null;

    protected $objectName = ''; // ClassBase

    protected $defaultNameSpace = '';
    protected $instanceTypeMap = array(
        BoxConstants::INSTANCE_TYPE_CACHE => BoxConstants::INSTANCE_CACHE_DRIVER_NONE, // 缓存
        BoxConstants::INSTANCE_TYPE_SEQ => BoxConstants::INSTANCE_SEQ_DRIVER_NONE, // 序列生成器
        BoxConstants::INSTANCE_TYPE_DB => BoxConstants::INSTANCE_DB_DRIVER_NONE, // 数据库
        BoxConstants::INSTANCE_TYPE_IDX => BoxConstants::INSTANCE_IDX_DRIVER_NONE, // 索引（全文检索）
        BoxConstants::INSTANCE_TYPE_MQ => BoxConstants::INSTANCE_MQ_DRIVER_NONE, // 消息队列
        BoxConstants::INSTANCE_TYPE_FS => BoxConstants::INSTANCE_FS_DRIVER_NONE, // 文件存储
        BoxConstants::INSTANCE_TYPE_RPC => BoxConstants::INSTANCE_RPC_DRIVER_NONE, // 远程调用
        BoxConstants::INSTANCE_TYPE_DC => BoxConstants::INSTANCE_DC_DRIVER_NONE, // 分布式计算
    );

    protected $instanceMap = array(/*
         * eg:
         * 'cache' =>  instance of \Nickfan\AppBox\Service\Drivers\RedisBoxRouteServiceDriver
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
            -10
        ); // -10 = strlen(BoxPackage)
        if (!empty($shortClassName)) {
            $objectName = lcfirst($shortClassName);
        } else {
            $objectName = '';
        }
        return array($className, $shortClassName, $objectName);
    }

    public static function getInstance(BoxRouteInstanceInterface $instBoxRouteInstance, $objectName = "") {
        if ($objectName == '') {
            list($className, $shortClassName, $objectName) = self::parseClassNameObjectName();
        }
        if (!isset(static::$instances[$objectName])) {
            static::$instances[$objectName] = new $className($instBoxRouteInstance, $objectName);
        }
        return static::$instances[$objectName];
    }

    protected function __construct(BoxRouteInstanceInterface $instBoxRouteInstance = null, $objectName = "") {
        if ($objectName == "") {
            list($className, $shortClassName, $objectName) = self::parseClassNameObjectName();
        } else {
            $this->objectName = $objectName;
        }
        if (is_null($instBoxRouteInstance)) {
            throw new RuntimeException('BoxPackage.BoxRouteInstance is null :' . get_class($this));
        } else {
            self::$routeInstance = $instBoxRouteInstance;
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

    public function getBoxRouteInstance() {
        return self::$routeInstance;
    }

    public function setBoxRouteInstance(BoxRouteInstanceInterface $instBoxRouteInstance) {
        self::$routeInstance = $instBoxRouteInstance;
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
        $driverClassName = '\\Nickfan\\AppBox\\Service\\Drivers\\' . $driverClassKey . 'BoxRouteServiceDriver';
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
    public function getBoxObjectTemplateByLabel($objectLabel = '', $defprops = array(), $namespace = '') {
        $retObject = array();
        $namespace == '' && $namespace = $this->getDefaultNamespace();
        if(!empty($namespace)){
            if(strlen($namespace)!=(strrpos($namespace,'\\')+1)){
                $namespace.='\\';
            }
        }
        $objectClassName = $namespace . ucfirst($objectLabel) . 'BoxObject';
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
        $objectClassName = $namespace . ucfirst($objectLabel) . 'BoxObject';

        if(array_key_exists(BoxObject::DO_VERSION_PROP, $reqData) && $reqData[BoxObject::DO_VERSION_PROP]!=$objectClassName::DO_VERSION){
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
            'mqInstance'=>$this->getInstanceTypeDriverName(BoxConstants::INSTANCE_TYPE_MQ), //队列服务实例
            'mqPrefix'=>static::$prefixMq,
            'options'=>array(),
            'encode'=>true,
            'dataRouteInstance'=>$this->getBoxRouteInstance(),
        );
        $retStatus = null;
        $message = $option['encode']==true?BoxUtil::datapack($data):$data;
        $driverInstance = $this->getSetInstanceTypeDriverInstance(BoxConstants::INSTANCE_TYPE_MQ);
        list($vendorInstance, $option) = $driverInstance->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $taskLabel,)
        );

        switch($option['mqInstance']){
            case BoxConstants::INSTANCE_MQ_DRIVER_REDIS:
                !isset($option['options']['type']) && $option['options']['type'] = 'list';
                if($option['options']['type']!='list'){
                    $retStatus = $vendorInstance->publish($option['mqPrefix'].$taskLabel,$message);
                }else{
                    $retStatus = $vendorInstance->lPush($option['mqPrefix'].$taskLabel,$message);
                }
                break;
            case BoxConstants::INSTANCE_MQ_DRIVER_BEANSTALK:
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
            'mqInstance'=>BoxConstants::INSTANCE_MQ_DRIVER_BEANSTALK, //队列服务实例
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
            case BoxConstants::INSTANCE_MQ_DRIVER_REDIS:
                // redis 这边需要默认socket 超时不限
                //@ini_set('default_socket_timeout', -1);
                !isset($option['options']['type']) && $option['options']['type'] = 'list';
                if($option['options']['type']!='list'){
                    $retData = $vendorInstance->subscribe($option['mqPrefix'].$taskLabel,$callback);
                }else{
                    $job = $vendorInstance->blPop($option['mqPrefix'].$taskLabel,$option['timeout']);
                    if($option['decode']==true){
                        $arg= BoxUtil::dataunpack($job);
                    }else{
                        $arg= $job;
                    }
                    $retData = call_user_func($callback, $arg);
                    //sleep($option['sleep']);
                }
                break;
            case BoxConstants::INSTANCE_MQ_DRIVER_BEANSTALK:
            default:
                try {
                    //$tubeStats = $vendorInstance->statsTube($option['mqPrefix'].$taskLabel);
                    //if($tubeStats){
                    $vendorInstance->watchOnly($option['mqPrefix'].$taskLabel);
                    $job = $vendorInstance->reserve($option['timeout']);
                    //var_dump($job);
                    if($job){
                        if($option['decode']==true){
                            $arg= BoxUtil::dataunpack($job->getData());
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
     * 根据ID列表批量获取数据
     * @param string $objectLabel
     * @param array $requestIds
     * @param array $option
     * @throws MyRuntimeException
     * @return Ambigous <NULL, mixed, multitype:, array>
     */
    protected function _getMultiDataByIds($objectLabel='',$requestIds=array(),$option=array()){
        $retDataStruct = array();
        $returnData = NULL;
        $option+=array(
            'collectionName' => NULL,  //指定特定的集合
            'dbName' => NULL,  //指定特定的库名
            'dbInstance'=>static::$dbInstanceType,	// 数据库实例
            'logObject'=>FALSE,		// 记录对象状态
            'useCache'=>TRUE,		// 从cache读取
            'setCache'=>TRUE,		// 写回cache
            'cacheInstance'=>static::$cacheInstanceType,	// cache实例类型(mem: memcache服务| redis: redis服务)
            'cachePrefix'=>static::$cachePrefix,
            'dbPrefix'=>static::$dbPrefix,
            'options'=>array(),
            'rowRouteDict'=>NULL,           // 行级路由字典
            'trimEmpty'=>TRUE,
            'noshards'=>FALSE,
        );

        $optionCache = array(
            'objectName'=>$objectLabel,
            'modLabel'=>$objectLabel,
        );
        $optionData = array(
            'objectName'=>$objectLabel,
            //'dbName'=>$objectLabel,
        );

        !empty($option['dbName']) && $optionData['dbName'] = $option['dbName'];
        $collectionName = !empty($option['collectionName']) ? $option['collectionName'] : $objectLabel;
        !empty($option['collectionName']) && $optionData['collectionName'] = $option['collectionName'];
        !empty($option['collectionName']) && $optionData['collection'] = $option['collectionName'];


        // @FIXME 暂时只支持固定类型
        // @notice 功能有限制
        // @TODO 需要跟进实现各个instance模式的实现

        // mongo对类型敏感，假设所有的ID是数字类型时，强制类型转换
        if(empty($requestIds)){
            return array($this->getBoxObjectTemplateByLabel($objectLabel));
        }
        $requestIds = array_map('intval', $requestIds);
        $requestIds = array_unique($requestIds,SORT_NUMERIC);
        $gotIdMap = array();
        $missIdMap = array();


        if($option['trimEmpty']==FALSE){
            foreach ($requestIds as $requestId){
                $retDataStruct[$requestId] = NULL;
            }
        }

        if($option['useCache']==TRUE){
            $cachekeys = array();
            foreach ($requestIds as $requestId){
                $cachekeys [$requestId]= $option['cachePrefix'].$objectLabel.KEYSEP.$requestId;
            }
            foreach ($requestIds as $requestId){
                if(!isset($gotIdMap[$requestId])){
                    $routeSetCache = array(
                        'key'=>$requestId,
                    );
                    $cacheDataStruct = $this->redisService->getMultiData($cachekeys,$optionCache);
                    if(!empty($cacheDataStruct)){
                        foreach ($cacheDataStruct as $cachekeyId=>$resRow){
                            $rowid = substr($cachekeyId, strlen($option['cachePrefix'].$objectLabel.KEYSEP));
                            $resRow !== FALSE && $resRow = BoxUtil::dataunpack($resRow);
                            if(!empty($resRow)){
                                $retDataStruct[$rowid] = $resRow;
                                $gotIdMap[$rowid] = $rowid;
                                unset($cachekeys[$rowid]);
                            }
                        }
                    }
                }
            }
        }

        foreach ($requestIds as $requestId){
            if(!isset($gotIdMap[$requestId])){
                $missIdMap[$requestId] = $requestId;
            }
        }
        if(!empty($missIdMap)){
            asort($missIdMap);
            if ($option['noshards']) {
                $rowOptionData = $optionData;
                $queryStruct=array(
                    'querySchema'=>array($objectLabel, ),
                    'conditionIn'=>array(
                        'id'=>array_values($missIdMap),
                    ),
                );
                $resAssoc = $this->mongoService->queryAssoc($queryStruct,$rowOptionData);
                if(!empty($resAssoc)){
                    foreach($resAssoc as $line=>$resRow){
                        if(!empty($resRow)){
                            if(isset($missIdMap[$resRow['id']])){
                                $retDataStruct[$resRow['id']] = $resRow;
                            }
                        }
                    }
                }
            } else {
                foreach($missIdMap as $requestId){
                    $routeSetData = array(
                        'id'=>$requestId,
                    );
                    $rowOptionData = $optionData;
                    $rowOptionData['routeSet'] = $routeSetData;
                    $queryStruct=array(
                        'querySchema'=>array($objectLabel, ),
                        'conditionIn'=>array(
                            'id'=>array_values($missIdMap),
                        ),
                    );
                    $resAssoc = $this->mongoService->queryAssoc($queryStruct,$rowOptionData);
                    if(!empty($resAssoc)){
                        foreach($resAssoc as $line=>$resRow){
                            if(!empty($resRow)){
                                if(isset($missIdMap[$resRow['id']])){
                                    $retDataStruct[$resRow['id']] = $resRow;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $retDataStruct;
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
            'dbInstance'=>$this->getInstanceTypeDriverName(BoxConstants::INSTANCE_TYPE_DB),	// 数据库实例
            'dbPrefix'=>static::$prefixDb,
            'dbOption'=>array(
//                'collectionName' => null,  //指定特定的集合
//                'dbName' => null,  //指定特定的库名
//                'options'=>array(),
            ),// 数据库选项
            'cacheInstance'=>$this->getInstanceTypeDriverName(BoxConstants::INSTANCE_TYPE_CACHE),	// cache实例类型
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
            return $this->getBoxObjectTemplateByLabel($objectLabel);
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
                case BoxConstants::INSTANCE_CACHE_DRIVER_MEMCACHE:
                    if($this->getInstanceTypeDriverName(BoxConstants::INSTANCE_TYPE_CACHE)==BoxConstants::INSTANCE_CACHE_DRIVER_MEMCACHE){
                        !isset($cacheDriverInstance) && $cacheDriverInstance = $this->getSetInstanceTypeDriverInstance(BoxConstants::INSTANCE_TYPE_CACHE);
                    }else{
                        !isset($cacheDriverInstance) && $cacheDriverInstance = self::initDriverInstanceByName(BoxConstants::INSTANCE_CACHE_DRIVER_MEMCACHE);
                        //$driverInstance->setRouteKey($this->getObjectName());
                        $cacheDriverInstance->setRouteKey($objectLabel);
                    }
                    $objectCacheKey = $option['cachePrefix'].$objectLabel.BoxConstants::KEYSEP.$requestId;
                    $returnData = $cacheDriverInstance->get($objectCacheKey,$optionCache);
                    break;
                case BoxConstants::INSTANCE_CACHE_DRIVER_REDIS:
                default:
                    if($this->getInstanceTypeDriverName(BoxConstants::INSTANCE_TYPE_CACHE)==BoxConstants::INSTANCE_CACHE_DRIVER_REDIS){
                        !isset($cacheDriverInstance) && $cacheDriverInstance = $this->getSetInstanceTypeDriverInstance(BoxConstants::INSTANCE_TYPE_CACHE);
                    }else{
                        !isset($cacheDriverInstance) && $cacheDriverInstance = self::initDriverInstanceByName(BoxConstants::INSTANCE_CACHE_DRIVER_REDIS);
                        //$driverInstance->setRouteKey($this->getObjectName());
                        $cacheDriverInstance->setRouteKey($objectLabel);
                    }
                    $objectCacheKey = $option['cachePrefix'].$objectLabel.BoxConstants::KEYSEP.$requestId;
                    $returnData = $cacheDriverInstance->get($objectCacheKey,$optionCache);
                    break;
            }
            $returnData !== false && $returnData = BoxUtil::dataunpack($returnData);
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
                case BoxConstants::INSTANCE_DB_DRIVER_MYSQL:
                case BoxConstants::INSTANCE_DB_DRIVER_MSSQL:
                    if(in_array($this->getInstanceTypeDriverName(BoxConstants::INSTANCE_TYPE_DB),array(BoxConstants::INSTANCE_DB_DRIVER_MYSQL,BoxConstants::INSTANCE_DB_DRIVER_MSSQL))){
                        !isset($dbDriverInstance) && $dbDriverInstance = $this->getSetInstanceTypeDriverInstance(BoxConstants::INSTANCE_TYPE_DB);
                    }else{
                        !isset($dbDriverInstance) && $dbDriverInstance = self::initDriverInstanceByName($option['dbInstance']);
                        //$dbDriverInstance->setRouteKey($this->getObjectName());
                        $dbDriverInstance->setRouteKey($objectLabel);
                    }
                    $queryStruct=array(
                        'querySchema'=>array($objectLabel, ),
                        'conditionKey'=>array(
                            $option['idLabel']=>$requestId,
                        ),
                    );
                    $returnData = $dbDriverInstance->queryRow($queryStruct,$optionDb);
                    break;
                case BoxConstants::INSTANCE_DB_DRIVER_REDIS:
                    if($this->getInstanceTypeDriverName(BoxConstants::INSTANCE_TYPE_DB)==BoxConstants::INSTANCE_DB_DRIVER_REDIS){
                        !isset($dbDriverInstance) && $dbDriverInstance = $this->getSetInstanceTypeDriverInstance(BoxConstants::INSTANCE_TYPE_DB);
                    }else{
                        !isset($dbDriverInstance) && $dbDriverInstance = self::initDriverInstanceByName(BoxConstants::INSTANCE_DB_DRIVER_REDIS);
                        //$dbDriverInstance->setRouteKey($this->getObjectName());
                        $dbDriverInstance->setRouteKey($objectLabel);
                    }
                    $objectDbKey = $option['dbPrefix'].$objectLabel.BoxConstants::KEYSEP.$requestId;
                    $returnData = $dbDriverInstance->get($objectDbKey,$optionDb);
                    $returnData !== false && $returnData = BoxUtil::dataunpack($returnData);
                    break;
                case BoxConstants::INSTANCE_DB_DRIVER_MONGODB:
                default:
                    $dbName = (isset($optionDb['dbName']) && !empty($optionDb['dbName']))?$optionDb['dbName']:lcfirst($objectLabel);
                    $collectionName = (isset($optionDb['collectionName']) && !empty($optionDb['collectionName']))?$optionDb['collectionName']:lcfirst($objectLabel);
                    if($this->getInstanceTypeDriverName(BoxConstants::INSTANCE_TYPE_DB)==BoxConstants::INSTANCE_DB_DRIVER_MONGODB){
                        !isset($dbDriverInstance) && $dbDriverInstance = $this->getSetInstanceTypeDriverInstance(BoxConstants::INSTANCE_TYPE_DB);
                    }else{
                        !isset($dbDriverInstance) && $dbDriverInstance = self::initDriverInstanceByName(BoxConstants::INSTANCE_DB_DRIVER_MONGODB);
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
                        case BoxConstants::INSTANCE_CACHE_DRIVER_MEMCACHE:
                            if($this->getInstanceTypeDriverName(BoxConstants::INSTANCE_TYPE_CACHE)==BoxConstants::INSTANCE_CACHE_DRIVER_MEMCACHE){
                                !isset($cacheDriverInstance) && $cacheDriverInstance = $this->getSetInstanceTypeDriverInstance(BoxConstants::INSTANCE_TYPE_CACHE);
                            }else{
                                !isset($cacheDriverInstance) && $cacheDriverInstance = self::initDriverInstanceByName(BoxConstants::INSTANCE_CACHE_DRIVER_MEMCACHE);
                                //$driverInstance->setRouteKey($this->getObjectName());
                                $cacheDriverInstance->setRouteKey($objectLabel);
                            }
                            $objectCacheKey = $option['cachePrefix'].$objectLabel.BoxConstants::KEYSEP.$requestId;
                            $cacheDriverInstance->set($objectCacheKey,BoxUtil::datapack($returnData),$option['cacheTtl'],$optionCache);
                            break;
                        case BoxConstants::INSTANCE_CACHE_DRIVER_REDIS:
                        default:
                            if($this->getInstanceTypeDriverName(BoxConstants::INSTANCE_TYPE_CACHE)==BoxConstants::INSTANCE_CACHE_DRIVER_REDIS){
                                !isset($cacheDriverInstance) && $cacheDriverInstance = $this->getSetInstanceTypeDriverInstance(BoxConstants::INSTANCE_TYPE_CACHE);
                            }else{
                                !isset($cacheDriverInstance) && $cacheDriverInstance = self::initDriverInstanceByName(BoxConstants::INSTANCE_CACHE_DRIVER_REDIS);
                                //$driverInstance->setRouteKey($this->getObjectName());
                                $cacheDriverInstance->setRouteKey($objectLabel);
                            }
                            $objectCacheKey = $option['cachePrefix'].$objectLabel.BoxConstants::KEYSEP.$requestId;
                            $cacheDriverInstance->setex($objectCacheKey,$option['cacheTtl'],BoxUtil::datapack($returnData),$optionCache);
                            break;
                    }
                }
            }
        }
        if($option['delCache']!==false){
            //删除cache
            switch($option['cacheInstance']){
                case BoxConstants::INSTANCE_CACHE_DRIVER_MEMCACHE:
                    if($this->getInstanceTypeDriverName(BoxConstants::INSTANCE_TYPE_CACHE)==BoxConstants::INSTANCE_CACHE_DRIVER_MEMCACHE){
                        !isset($cacheDriverInstance) && $cacheDriverInstance = $this->getSetInstanceTypeDriverInstance(BoxConstants::INSTANCE_TYPE_CACHE);
                    }else{
                        !isset($cacheDriverInstance) && $cacheDriverInstance = self::initDriverInstanceByName(BoxConstants::INSTANCE_CACHE_DRIVER_MEMCACHE);
                        //$driverInstance->setRouteKey($this->getObjectName());
                        $cacheDriverInstance->setRouteKey($objectLabel);
                    }
                    $objectCacheKey = $option['cachePrefix'].$objectLabel.BoxConstants::KEYSEP.$requestId;
                    $cacheDriverInstance->delete($objectCacheKey,0,$optionCache);
                    break;
                case BoxConstants::INSTANCE_CACHE_DRIVER_REDIS:
                default:
                    if($this->getInstanceTypeDriverName(BoxConstants::INSTANCE_TYPE_CACHE)==BoxConstants::INSTANCE_CACHE_DRIVER_REDIS){
                        !isset($cacheDriverInstance) && $cacheDriverInstance = $this->getSetInstanceTypeDriverInstance(BoxConstants::INSTANCE_TYPE_CACHE);
                    }else{
                        !isset($cacheDriverInstance) && $cacheDriverInstance = self::initDriverInstanceByName(BoxConstants::INSTANCE_CACHE_DRIVER_REDIS);
                        //$driverInstance->setRouteKey($this->getObjectName());
                        $cacheDriverInstance->setRouteKey($objectLabel);
                    }
                    $objectCacheKey = $option['cachePrefix'].$objectLabel.BoxConstants::KEYSEP.$requestId;
                    $cacheDriverInstance->delete($objectCacheKey,$optionCache);
                    break;
            }
        }
        return $returnData;
    }


    /**
     * 根据数据类型标识、对象ID更新对应对象数据
     * @param string $objectLabel
     * @param int $requestId
     * @param array $reqData
     * @param array $option
     * @return array|mixed|null
     */
    public function _setStructDataById($objectLabel='',$requestId=0,$reqData=array(),$option=array()){
        $returnData = null;
        $option+=array(
            'idLabel'=>'id',    // 自定义idlabel （字段名）
            'dbInstance'=>$this->getInstanceTypeDriverName(BoxConstants::INSTANCE_TYPE_DB),	// 数据库实例
            'dbPrefix'=>static::$prefixDb,
            'dbOption'=>array(
//                'collectionName' => null,  //指定特定的集合
//                'dbName' => null,  //指定特定的库名
//                'options'=>array(),
            ),// 数据库选项
            'cacheInstance'=>$this->getInstanceTypeDriverName(BoxConstants::INSTANCE_TYPE_CACHE),	// cache实例类型
            'setCache'=>null,		// NULL 清空cache | TRUE 写入cache | FALSE 不设定、使用cache
            'cacheOption'=>array(),// 缓存路由选项
            'cachePrefix'=>static::$prefixCache,
            'cacheTtl'=>static::$ttlCache,

        );
        // mongo对类型敏感，假设所有的ID是数字类型时，强制类型转换
        is_numeric($requestId) && $requestId = intval($requestId);

        $storeData = $tplRowData = $this->getBoxObjectTemplateByLabel($objectLabel);
        $upData = array();
        $gotServiceSyntax = false;
        foreach ($reqData as $reqKey => $reqValue){
            if($option['dbInstance']==BoxConstants::INSTANCE_DB_DRIVER_MONGODB && MongoBoxRouteServiceDriver::isServiceSyntax($reqKey)){
                $gotServiceSyntax = true;
                break;
            }elseif(in_array($option['dbInstance'],array(BoxConstants::INSTANCE_DB_DRIVER_MYSQL,BoxConstants::INSTANCE_DB_DRIVER_MSSQL)) && DbBoxRouteServiceDriver::hasOperator($reqKey)){
                $gotServiceSyntax = true;
                break;
            }
            else{
                if(array_key_exists($reqKey, $tplRowData)){
                    $upData[$reqKey] = $reqValue;
                }
            }
        }
        if($gotServiceSyntax==true){
            $upData = $reqData;
        }else{
            $storeData = array_merge($storeData,$upData);
        }
        $optionDb = array(
            'routeKey'=>$objectLabel,
        );
        !empty($option['dbOption']) && $optionDb = array_merge($optionDb,$option['dbOption']);

        switch($option['dbInstance']){
            case BoxConstants::INSTANCE_DB_DRIVER_MYSQL:
            case BoxConstants::INSTANCE_DB_DRIVER_MSSQL:
                if(in_array($this->getInstanceTypeDriverName(BoxConstants::INSTANCE_TYPE_DB),array(BoxConstants::INSTANCE_DB_DRIVER_MYSQL,BoxConstants::INSTANCE_DB_DRIVER_MSSQL))){
                    !isset($dbDriverInstance) && $dbDriverInstance = $this->getSetInstanceTypeDriverInstance(BoxConstants::INSTANCE_TYPE_DB);
                }else{
                    !isset($dbDriverInstance) && $dbDriverInstance = self::initDriverInstanceByName($option['dbInstance']);
                    //$dbDriverInstance->setRouteKey($this->getObjectName());
                    $dbDriverInstance->setRouteKey($objectLabel);
                }
                if($option['dbInstance']==BoxConstants::INSTANCE_DB_DRIVER_MSSQL){
                    $dbType = DbBoxRouteServiceDriver::DBTYPE_MSSQL;
                }elseif($option['dbInstance']==BoxConstants::INSTANCE_DB_DRIVER_MYSQL){
                    $dbType = DbBoxRouteServiceDriver::DBTYPE_MYSQL;
                }

                if($gotServiceSyntax==TRUE){
                    $queryFieldStruct = array();
                    foreach ($upData as $key=>$val){
                        if(DbBoxRouteServiceDriver::hasOperator($key)){
                            $queryFieldStruct[] = $key . $val;
                        }else{
                            $queryFieldStruct[] = $key . ' = '. DbBoxRouteServiceDriver::dbEscape($val,$dbType);
                        }
                    }
                }else{
                    $queryFieldStruct = array();
                    foreach ($upData as $key=>$val){
                        $queryFieldStruct[] = $key . ' = '. DbBoxRouteServiceDriver::dbEscape($val,$dbType);
                    }
                }
                $queryStruct=array(
                    'queryField'=>$queryFieldStruct,
                    'querySchema'=>array($objectLabel, ),
                    'conditionKey'=>array(
                        //'id'=>$requestId,
                    ),
                );
                $queryStruct['conditionKey'][$option['idLabel']] = $requestId;
                $returnData = $dbDriverInstance->queryUpdate($queryStruct,$optionDb);
                break;
            case BoxConstants::INSTANCE_DB_DRIVER_REDIS:
                if($this->getInstanceTypeDriverName(BoxConstants::INSTANCE_TYPE_DB)==BoxConstants::INSTANCE_DB_DRIVER_REDIS){
                    !isset($dbDriverInstance) && $dbDriverInstance = $this->getSetInstanceTypeDriverInstance(BoxConstants::INSTANCE_TYPE_DB);
                }else{
                    !isset($dbDriverInstance) && $dbDriverInstance = self::initDriverInstanceByName(BoxConstants::INSTANCE_DB_DRIVER_REDIS);
                    //$dbDriverInstance->setRouteKey($this->getObjectName());
                    $dbDriverInstance->setRouteKey($objectLabel);
                }
                $objectDbKey = $option['dbPrefix'].$objectLabel.BoxConstants::KEYSEP.$requestId;
                $returnData = $dbDriverInstance->set($objectDbKey,BoxUtil::datapack($reqData),$optionDb);
                break;
            case BoxConstants::INSTANCE_DB_DRIVER_MONGODB:
            default:
                $dbName = (isset($optionDb['dbName']) && !empty($optionDb['dbName']))?$optionDb['dbName']:lcfirst($objectLabel);
                $collectionName = (isset($optionDb['collectionName']) && !empty($optionDb['collectionName']))?$optionDb['collectionName']:lcfirst($objectLabel);
                if($this->getInstanceTypeDriverName(BoxConstants::INSTANCE_TYPE_DB)==BoxConstants::INSTANCE_DB_DRIVER_MONGODB){
                    !isset($dbDriverInstance) && $dbDriverInstance = $this->getSetInstanceTypeDriverInstance(BoxConstants::INSTANCE_TYPE_DB);
                }else{
                    !isset($dbDriverInstance) && $dbDriverInstance = self::initDriverInstanceByName(BoxConstants::INSTANCE_DB_DRIVER_MONGODB);
                    $dbDriverInstance->__init(array('routeKey'=>$objectLabel,'dbName'=>$dbName));
                }
                $dbOptions = isset($optionDb['options']) && !empty($optionDb['options']) ? $optionDb['options']:array();
                empty($dbOptions) && $dbOptions = array('safe'=>true,);

                $optionReq = $optionDb;
                $optionReq['idLabel'] = $option['idLabel'];

                $queryIdCond = array($option['idLabel'] => $requestId);
                $dbData = $dbDriverInstance->read($queryIdCond,$optionReq);
                if(empty($dbData)){
                    // 如果未找到数据时直接返回
                    return false;
                }
                $storeData = array_merge($storeData,$dbData);
                $setData = array();
                $gotServiceSyntax = false;
                foreach ($reqData as $reqKey => $reqValue){
                    if(MongoBoxRouteServiceDriver::isServiceSyntax($reqKey)){
                        $gotServiceSyntax = true;
                        break;
                    }else{
                        $reqKey!=$option['idLabel'] && array_key_exists($reqKey,$dbData) && $dbData[$reqKey]!==$reqValue && $setData[$reqKey] = $reqValue;
                    }
                }
                $collectionData = $dbDriverInstance->selectCollection($dbName,$collectionName,$optionDb);
                if($gotServiceSyntax==true){
                    $setData = $reqData;
                    $collectionData->update($queryIdCond, $setData,$dbOptions);
                }else{
                    if (empty($setData)) {
                        return FALSE;
                    }
                    $collectionData->update($queryIdCond, array('$set' => $setData),$dbOptions);
                }
                if($gotServiceSyntax==false){
                    $storeData = array_merge($storeData,$upData);
                }
                break;
        }

        if($option['setCache']!==false){
            $optionCache = array(
                'routeKey'=>$objectLabel,
            );
            !empty($option['cacheOption']) && $optionCache = array_merge($optionCache,$option['cacheOption']);
            if($option['setCache']===true){
                // 写入缓存
                switch($option['cacheInstance']){
                    case BoxConstants::INSTANCE_CACHE_DRIVER_MEMCACHE:
                        if($this->getInstanceTypeDriverName(BoxConstants::INSTANCE_TYPE_CACHE)==BoxConstants::INSTANCE_CACHE_DRIVER_MEMCACHE){
                            !isset($cacheDriverInstance) && $cacheDriverInstance = $this->getSetInstanceTypeDriverInstance(BoxConstants::INSTANCE_TYPE_CACHE);
                        }else{
                            !isset($cacheDriverInstance) && $cacheDriverInstance = self::initDriverInstanceByName(BoxConstants::INSTANCE_CACHE_DRIVER_MEMCACHE);
                            //$driverInstance->setRouteKey($this->getObjectName());
                            $cacheDriverInstance->setRouteKey($objectLabel);
                        }
                        $objectCacheKey = $option['cachePrefix'].$objectLabel.BoxConstants::KEYSEP.$requestId;
                        $cacheDriverInstance->set($objectCacheKey,BoxUtil::datapack($storeData),$option['cacheTtl'],$optionCache);
                        break;
                    case BoxConstants::INSTANCE_CACHE_DRIVER_REDIS:
                    default:
                        if($this->getInstanceTypeDriverName(BoxConstants::INSTANCE_TYPE_CACHE)==BoxConstants::INSTANCE_CACHE_DRIVER_REDIS){
                            !isset($cacheDriverInstance) && $cacheDriverInstance = $this->getSetInstanceTypeDriverInstance(BoxConstants::INSTANCE_TYPE_CACHE);
                        }else{
                            !isset($cacheDriverInstance) && $cacheDriverInstance = self::initDriverInstanceByName(BoxConstants::INSTANCE_CACHE_DRIVER_REDIS);
                            //$driverInstance->setRouteKey($this->getObjectName());
                            $cacheDriverInstance->setRouteKey($objectLabel);
                        }
                        $objectCacheKey = $option['cachePrefix'].$objectLabel.BoxConstants::KEYSEP.$requestId;
                        $cacheDriverInstance->setex($objectCacheKey,$option['cacheTtl'],BoxUtil::datapack($storeData),$optionCache);
                        break;
                }
            }else{
                //删除cache
                switch($option['cacheInstance']){
                    case BoxConstants::INSTANCE_CACHE_DRIVER_MEMCACHE:
                        if($this->getInstanceTypeDriverName(BoxConstants::INSTANCE_TYPE_CACHE)==BoxConstants::INSTANCE_CACHE_DRIVER_MEMCACHE){
                            !isset($cacheDriverInstance) && $cacheDriverInstance = $this->getSetInstanceTypeDriverInstance(BoxConstants::INSTANCE_TYPE_CACHE);
                        }else{
                            !isset($cacheDriverInstance) && $cacheDriverInstance = self::initDriverInstanceByName(BoxConstants::INSTANCE_CACHE_DRIVER_MEMCACHE);
                            //$driverInstance->setRouteKey($this->getObjectName());
                            $cacheDriverInstance->setRouteKey($objectLabel);
                        }
                        $objectCacheKey = $option['cachePrefix'].$objectLabel.BoxConstants::KEYSEP.$requestId;
                        $cacheDriverInstance->delete($objectCacheKey,0,$optionCache);
                        break;
                    case BoxConstants::INSTANCE_CACHE_DRIVER_REDIS:
                    default:
                        if($this->getInstanceTypeDriverName(BoxConstants::INSTANCE_TYPE_CACHE)==BoxConstants::INSTANCE_CACHE_DRIVER_REDIS){
                            !isset($cacheDriverInstance) && $cacheDriverInstance = $this->getSetInstanceTypeDriverInstance(BoxConstants::INSTANCE_TYPE_CACHE);
                        }else{
                            !isset($cacheDriverInstance) && $cacheDriverInstance = self::initDriverInstanceByName(BoxConstants::INSTANCE_CACHE_DRIVER_REDIS);
                            //$driverInstance->setRouteKey($this->getObjectName());
                            $cacheDriverInstance->setRouteKey($objectLabel);
                        }
                        $objectCacheKey = $option['cachePrefix'].$objectLabel.BoxConstants::KEYSEP.$requestId;
                        $cacheDriverInstance->delete($objectCacheKey,$optionCache);
                        break;
                }
            }
        }
        return $returnData;
    }

    /**
     * 根据数据类型标识、对象ID删除对应对象数据
     * @param @param string $objectLabel
     * @param int $requestId
     * @param array $option
     * @throws MyRuntimeException
     * @return array|null
     */
    public function _delStructDataById($objectLabel='',$requestId=0,$option=array()){
        $returnData = array(
            'db'=>false,
            'cache'=>false,
        );
        $option+=array(
            'idLabel'=>'id',    // 自定义idlabel （字段名）
            'dbInstance'=>$this->getInstanceTypeDriverName(BoxConstants::INSTANCE_TYPE_DB),	// 数据库实例
            'dbPrefix'=>static::$prefixDb,
            'dbOption'=>array(
//                'collectionName' => null,  //指定特定的集合
//                'dbName' => null,  //指定特定的库名
//                'options'=>array(),
            ),// 数据库选项
            'cacheInstance'=>$this->getInstanceTypeDriverName(BoxConstants::INSTANCE_TYPE_CACHE),	// cache实例类型
            'setCache'=>null,		// NULL 清空cache | TRUE 写入cache | FALSE 不设定、使用cache
            'cacheOption'=>array(),// 缓存路由选项
            'cachePrefix'=>static::$prefixCache,
            'cacheTtl'=>static::$ttlCache,

        );
        // mongo对类型敏感，假设所有的ID是数字类型时，强制类型转换
        is_numeric($requestId) && $requestId = intval($requestId);

        $optionDb = array(
            'routeKey'=>$objectLabel,
        );
        !empty($option['dbOption']) && $optionDb = array_merge($optionDb,$option['dbOption']);

        switch($option['dbInstance']){
            case BoxConstants::INSTANCE_DB_DRIVER_MYSQL:
            case BoxConstants::INSTANCE_DB_DRIVER_MSSQL:
                if(in_array($this->getInstanceTypeDriverName(BoxConstants::INSTANCE_TYPE_DB),array(BoxConstants::INSTANCE_DB_DRIVER_MYSQL,BoxConstants::INSTANCE_DB_DRIVER_MSSQL))){
                    !isset($dbDriverInstance) && $dbDriverInstance = $this->getSetInstanceTypeDriverInstance(BoxConstants::INSTANCE_TYPE_DB);
                }else{
                    !isset($dbDriverInstance) && $dbDriverInstance = self::initDriverInstanceByName($option['dbInstance']);
                    //$dbDriverInstance->setRouteKey($this->getObjectName());
                    $dbDriverInstance->setRouteKey($objectLabel);
                }
                $queryStruct=array(
                    'querySchema'=>array($objectLabel, ),
                    'conditionKey'=>array(
                        $option['idlabel']=>$requestId,
                    ),
                );
                $returnData['db'] = $dbDriverInstance->queryDelete($queryStruct,$optionDb);
                break;
            case BoxConstants::INSTANCE_DB_DRIVER_REDIS:
                if($this->getInstanceTypeDriverName(BoxConstants::INSTANCE_TYPE_DB)==BoxConstants::INSTANCE_DB_DRIVER_REDIS){
                    !isset($dbDriverInstance) && $dbDriverInstance = $this->getSetInstanceTypeDriverInstance(BoxConstants::INSTANCE_TYPE_DB);
                }else{
                    !isset($dbDriverInstance) && $dbDriverInstance = self::initDriverInstanceByName(BoxConstants::INSTANCE_DB_DRIVER_REDIS);
                    //$dbDriverInstance->setRouteKey($this->getObjectName());
                    $dbDriverInstance->setRouteKey($objectLabel);
                }
                $objectDbKey = $option['dbPrefix'].$objectLabel.BoxConstants::KEYSEP.$requestId;
                $returnData['db'] = $dbDriverInstance->delete($objectDbKey,$optionDb);
                break;
            case BoxConstants::INSTANCE_DB_DRIVER_MONGODB:
            default:
                $dbName = (isset($optionDb['dbName']) && !empty($optionDb['dbName']))?$optionDb['dbName']:lcfirst($objectLabel);
                $collectionName = (isset($optionDb['collectionName']) && !empty($optionDb['collectionName']))?$optionDb['collectionName']:lcfirst($objectLabel);
                if($this->getInstanceTypeDriverName(BoxConstants::INSTANCE_TYPE_DB)==BoxConstants::INSTANCE_DB_DRIVER_MONGODB){
                    !isset($dbDriverInstance) && $dbDriverInstance = $this->getSetInstanceTypeDriverInstance(BoxConstants::INSTANCE_TYPE_DB);
                }else{
                    !isset($dbDriverInstance) && $dbDriverInstance = self::initDriverInstanceByName(BoxConstants::INSTANCE_DB_DRIVER_MONGODB);
                    $dbDriverInstance->__init(array('routeKey'=>$objectLabel,'dbName'=>$dbName));
                }
                $optionReq = $optionDb;
                $optionReq['idLabel'] = $option['idLabel'];
                $returnData['db'] = $dbDriverInstance->delete(array($option['idLabel']=>$requestId),$optionReq);
                break;
        }

        if($option['setCache']!==false){
            $optionCache = array(
                'routeKey'=>$objectLabel,
            );
            switch($option['cacheInstance']){
                case BoxConstants::INSTANCE_CACHE_DRIVER_MEMCACHE:
                    if($this->getInstanceTypeDriverName(BoxConstants::INSTANCE_TYPE_CACHE)==BoxConstants::INSTANCE_CACHE_DRIVER_MEMCACHE){
                        !isset($cacheDriverInstance) && $cacheDriverInstance = $this->getSetInstanceTypeDriverInstance(BoxConstants::INSTANCE_TYPE_CACHE);
                    }else{
                        !isset($cacheDriverInstance) && $cacheDriverInstance = self::initDriverInstanceByName(BoxConstants::INSTANCE_CACHE_DRIVER_MEMCACHE);
                        //$driverInstance->setRouteKey($this->getObjectName());
                        $cacheDriverInstance->setRouteKey($objectLabel);
                    }
                    $objectCacheKey = $option['cachePrefix'].$objectLabel.BoxConstants::KEYSEP.$requestId;
                    $returnData['cache'] = $cacheDriverInstance->delete($objectCacheKey,$optionCache);
                    break;
                case BoxConstants::INSTANCE_CACHE_DRIVER_REDIS:
                default:
                    if($this->getInstanceTypeDriverName(BoxConstants::INSTANCE_TYPE_CACHE)==BoxConstants::INSTANCE_CACHE_DRIVER_REDIS){
                        !isset($cacheDriverInstance) && $cacheDriverInstance = $this->getSetInstanceTypeDriverInstance(BoxConstants::INSTANCE_TYPE_CACHE);
                    }else{
                        !isset($cacheDriverInstance) && $cacheDriverInstance = self::initDriverInstanceByName(BoxConstants::INSTANCE_CACHE_DRIVER_REDIS);
                        //$driverInstance->setRouteKey($this->getObjectName());
                        $cacheDriverInstance->setRouteKey($objectLabel);
                    }
                    $objectCacheKey = $option['cachePrefix'].$objectLabel.BoxConstants::KEYSEP.$requestId;
                    $returnData['cache'] = $cacheDriverInstance->delete($objectCacheKey,$optionCache);
                    break;
            }
        }
        return $returnData;
    }


    /**
     * 根据数据类型标识、对象ID清除对应对象数据缓存
     * @param @param string $objectLabel
     * @param int $requestId
     * @param array $option
     * @throws MyRuntimeException
     * @return array|null
     */
    public function _clearCacheStructDataById($objectLabel='',$requestId=0,$option=array()){
        $returnData = array(
            'cache'=>false,
        );
        $option+=array(
            'idLabel'=>'id',    // 自定义idlabel （字段名）
            'cacheInstance'=>$this->getInstanceTypeDriverName(BoxConstants::INSTANCE_TYPE_CACHE),	// cache实例类型
            'setCache'=>null,		// NULL 清空cache | TRUE 写入cache | FALSE 不设定、使用cache
            'cacheOption'=>array(),// 缓存路由选项
            'cachePrefix'=>static::$prefixCache,
            'cacheTtl'=>static::$ttlCache,

        );
        // mongo对类型敏感，假设所有的ID是数字类型时，强制类型转换
        is_numeric($requestId) && $requestId = intval($requestId);

        if($option['setCache']!==false){
            $optionCache = array(
                'routeKey'=>$objectLabel,
            );
            switch($option['cacheInstance']){
                case BoxConstants::INSTANCE_CACHE_DRIVER_MEMCACHE:
                    if($this->getInstanceTypeDriverName(BoxConstants::INSTANCE_TYPE_CACHE)==BoxConstants::INSTANCE_CACHE_DRIVER_MEMCACHE){
                        !isset($cacheDriverInstance) && $cacheDriverInstance = $this->getSetInstanceTypeDriverInstance(BoxConstants::INSTANCE_TYPE_CACHE);
                    }else{
                        !isset($cacheDriverInstance) && $cacheDriverInstance = self::initDriverInstanceByName(BoxConstants::INSTANCE_CACHE_DRIVER_MEMCACHE);
                        //$driverInstance->setRouteKey($this->getObjectName());
                        $cacheDriverInstance->setRouteKey($objectLabel);
                    }
                    $objectCacheKey = $option['cachePrefix'].$objectLabel.BoxConstants::KEYSEP.$requestId;
                    $returnData['cache'] = $cacheDriverInstance->delete($objectCacheKey,$optionCache);
                    break;
                case BoxConstants::INSTANCE_CACHE_DRIVER_REDIS:
                default:
                    if($this->getInstanceTypeDriverName(BoxConstants::INSTANCE_TYPE_CACHE)==BoxConstants::INSTANCE_CACHE_DRIVER_REDIS){
                        !isset($cacheDriverInstance) && $cacheDriverInstance = $this->getSetInstanceTypeDriverInstance(BoxConstants::INSTANCE_TYPE_CACHE);
                    }else{
                        !isset($cacheDriverInstance) && $cacheDriverInstance = self::initDriverInstanceByName(BoxConstants::INSTANCE_CACHE_DRIVER_REDIS);
                        //$driverInstance->setRouteKey($this->getObjectName());
                        $cacheDriverInstance->setRouteKey($objectLabel);
                    }
                    $objectCacheKey = $option['cachePrefix'].$objectLabel.BoxConstants::KEYSEP.$requestId;
                    $returnData['cache'] = $cacheDriverInstance->delete($objectCacheKey,$optionCache);
                    break;
            }
        }
        return $returnData;
    }

}
