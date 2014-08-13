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


use Nickfan\AppBox\Common\AppConstants;
use Nickfan\AppBox\Service\BaseDataRouteServiceDriver;
use Nickfan\AppBox\Service\DataRouteServiceDriverInterface;
use Nickfan\AppBox\Support\Util;

class MongoDataRouteServiceDriver extends BaseDataRouteServiceDriver implements DataRouteServiceDriverInterface {
    const RETURNTYPE_DATA = 1;
    const RETURNTYPE_CURSOR = 2;

    //protected static $driverKey = 'mongo';

    protected $dbName = '';

    public function __init($params=array()){
        $params+=array(
            'routeKey'=>AppConstants::CONF_KEY_ROOT,
            'dbName'=>null,
        );
        $this->setRouteKey($params['routeKey']);
        if(empty($params['dbName'])){
            if(empty($this->dbName)){
                $dbName = lcfirst($params['routeKey']);
                $this->setDbName($dbName);
            }
        }else{
            $this->setDbName($params['dbName']);
        }
    }

    public function getDbName() {
        return $this->dbName;
    }

    public function setDbName($dbName) {
        $this->dbName = $dbName;
    }
    /**
     * 是否为服务语法
     * @param string $cmd
     * @return boolean
     */
    public static function isServiceSyntax($cmd){
        return $cmd{0}=='$';
    }

    /**
     * 获取Mongo的数据库对象
     */
    public function selectDB($dbName=null, $option = array(), $vendorInstance = null) {
        empty($dbName) && $dbName = $this->getDbName();
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        return $vendorInstance->selectDB($dbName);
    }
    /**
     * 获取Mongo的数据列表对象
     */
    public function selectCollection($dbName=null,$collectionName=null, $option = array(), $vendorInstance = null) {
        empty($dbName) && $dbName = $this->getDbName();
        empty($collectionName) && $collectionName = $this->getDbName();
        $option += array(
        );
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        return $vendorInstance->selectCollection($dbName,$collectionName);
    }
    /**
     * 获取Mongo的数据列表对象(GridFS)
     */
    public function getGridFS($prefix='fs', $option = array(), $vendorInstance = null) {
        $option += array(
            'dbName'=> $this->getDbName(),
        );
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        $dbObj = $vendorInstance->selectDB($option['dbName']);
        return $dbObj->getGridFS($prefix);
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
            'init' => 1,
            'step' => 1,
            'dbName'=>null,
        );
        $option['init'] = intval($option['init']);
        $option['step'] = intval($option['step']);
        empty($objectName) && $objectName = $this->getRouteKey();
        $seqModLabel = lcfirst($option['routeKey']);

        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $objectName,)
        );

        $seqCollectionName = $seqModLabel;

        if(empty($option['dbName'])){
            $seqDbName = '_'.$seqModLabel;
        }else{
            $seqDbName = $option['dbName'];
        }
        $instance = $vendorInstance->selectCollection($seqDbName,$seqCollectionName);

        $seq = $instance->db->command(array(
                'findAndModify' => 'seq',
                'query' => array('_id' => $objectName),
                'update' => array('$inc' => array('id' => $option['step'])),
                'new' => true,
            ),array('safe'=>TRUE,));
        if (isset($seq['value']['id'])) {
            return $seq['value']['id'];
        }else{
            if(is_null($seq['value']['id'])){
                $seq = $instance->db->command(array(
                        'findAndModify' => 'seq',
                        'query' => array('_id' => $objectName),
                        'update' => array('$set' => array('id' => $option['init'])),
                        'new' => true,
                    ),array('safe'=>TRUE,));
            }else{
                $instance->insert(array('_id' => $objectName,'id' => $option['init'],),array('safe'=>TRUE,));
            }
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
            'init' => 1,
            //'step' => 1,
            'dbName'=>null,
        );
        $option['init'] = intval($option['init']);
        $option['step'] = intval($option['step']);
        empty($objectName) && $objectName = $this->getRouteKey();
        $seqModLabel = lcfirst($option['routeKey']);

        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $objectName,)
        );

        $seqCollectionName = $seqModLabel;

        if(empty($option['dbName'])){
            $seqDbName = '_'.$seqModLabel;
        }else{
            $seqDbName = $option['dbName'];
        }
        $instance = $vendorInstance->selectCollection($seqDbName,$seqCollectionName);

        $getData = $instance->findOne(array('_id' => $objectName));
        if(!empty($getData)){
            return $getData['id'];
        }else{
            return $option['init'];
        }
    }


    /**
     * 设置序列id
     * @param string $objectName
     * @param array $option
     * @param null $vendorInstance
     * @return int
     */
    public function setSeq($objectName = '',$option = array(), $vendorInstance = null){
        $option += array(
            'routeKey'=>'Seq',
            'init' => 1,
            //'step' => 1,
            'dbName'=>null,
        );
        $option['init'] = intval($option['init']);
        $option['step'] = intval($option['step']);
        empty($objectName) && $objectName = $this->getRouteKey();
        $seqModLabel = lcfirst($option['routeKey']);

        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $objectName,)
        );

        $seqCollectionName = $seqModLabel;

        if(empty($option['dbName'])){
            $seqDbName = '_'.$seqModLabel;
        }else{
            $seqDbName = $option['dbName'];
        }
        $instance = $vendorInstance->selectCollection($seqDbName,$seqCollectionName);

        $seq = $instance->db->command(array(
                'findAndModify' => 'seq',
                'query' => array('_id' => $objectName),
                'update' => array('$set' => array('id' => $option['init'])),
                'new' => true,
            ),array('safe'=>TRUE,));
        //print(PHP_EOL.'<pre>'.PHP_EOL.Lemon::debug(__FUNCTION__,$option,$objectName,$seq).PHP_EOL.'</pre>'.PHP_EOL);  exit; // [DEV-DEBUG]---
        if (isset($seq['value']['id'])) {
            return $seq['value']['id'];
        }else{
            if(is_null($seq['value']['id'])){
                $seq = $instance->db->command(array(
                        'findAndModify' => 'seq',
                        'query' => array('_id' => $objectName),
                        'update' => array('$set' => array('id' => $option['init'])),
                        'new' => true,
                    ),array('safe'=>TRUE,));
            }else{
                $instance->insert(array('_id' => $objectName,'id' => $option['init'],),array('safe'=>TRUE,));
            }
            return $option['init'];
        }
    }


    /**
     * 设定查询统计数缓存
     * @param   string  $objectName 对象名称
     * @param   array   $option     可选设定，下标init 初始值，默认1，下标step 步长，默认1
     * @return int
     */
    public function setQueryCount($objectName = '', $conditionHash='', $init=0, $option = array(), $vendorInstance = null){
        $option += array(
            'routeKey'=>'QueryCount',
            'step' => 1,
            'timeout' => 300,
            'curts'=>time(),
        );
        $option['step'] = intval($option['step']);
        empty($objectName) && $objectName = $this->getRouteKey();
        $qcountDbName = '_qcount';
        $qcountCollectionName = 'qc_'.$objectName;
        $qcountId = $conditionHash;

        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $objectName,)
        );
        $instance = $vendorInstance->selectCollection($qcountDbName,$qcountCollectionName);

        $qcount = $instance->db->command(array(
                'findAndModify' => $qcountCollectionName,
                'query' => array('_id' => $qcountId),
                'update' => array('$set' => array('id' => $init,'exp'=>$option['curts']+$option['timeout'],)),
                'new' => true,
            ),array('safe'=>true,));
        if (isset($qcount['value']['id'])) {
            return $qcount['value']['id'];
        }else{
            $instance->insert(array('_id' => $qcountId,'id' => $init,'exp'=>$option['curts']+$option['timeout'],),array('safe'=>true,));
            return $init;
        }
    }

    /**
     * 设定查询统计数缓存
     * @param   string  $objectName 对象名称
     * @param   array   $option     可选设定，下标init 初始值，默认1，下标step 步长，默认1
     * @return int
     */
    public function nextQueryCount($objectName = '', $conditionHash='',$option = array(), $vendorInstance = null){
        $option += array(
            'routeKey'=>'QueryCount',
            'init' => 0,
            'step' => 1,
            'timeout' => 300,
            'curts'=>time(),
        );
        $option['step'] = intval($option['step']);
        empty($objectName) && $objectName = $this->getRouteKey();
        $qcountDbName = '_qcount';
        $qcountCollectionName = 'qc_'.$objectName;
        $qcountId = $conditionHash;

        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $objectName,)
        );
        $instance = $vendorInstance->selectCollection($qcountDbName,$qcountCollectionName);

        $qcount = $instance->db->command(array(
                'findAndModify' => $qcountCollectionName,
                'query' => array('_id' => $qcountId),
                'update' => array('$inc' => array('id' => $option['step']),'$set'=>array('exp'=>$option['curts']+$option['timeout'],)),
                'new' => true,
            ),array('safe'=>TRUE,));
        if (isset($qcount['value']['id'])) {
            return $qcount['value']['id'];
        }else{

            $instance->insert(array('_id' => $qcountId,'id' => $option['init'],'exp'=>$option['curts']+$option['timeout'],),array('safe'=>TRUE,));
            return $option['init'];
        }
    }

    /**
     * 设定查询统计数缓存
     * @param   string  $objectName 对象名称
     * @param   array   $option     可选设定，下标init 初始值，默认1，下标step 步长，默认1
     * @return int
     */
    public function currentQueryCount($objectName = '', $conditionHash='',$option = array(), $vendorInstance = null){
        $option += array(
            'routeKey'=>'QueryCount',
            //'init' => 0,
            //'step' => 1,
            'timeout' => 300,
            'curts'=>time(),
        );
        $option['step'] = intval($option['step']);
        empty($objectName) && $objectName = $this->getRouteKey();
        $qcountDbName = '_qcount';
        $qcountCollectionName = 'qc_'.$objectName;
        $qcountId = $conditionHash;

        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $objectName,)
        );
        $instance = $vendorInstance->selectCollection($qcountDbName,$qcountCollectionName);

        $getData = $instance->findOne(array('_id' => $qcountId));
        if(!empty($getData)){
            if($getData['exp']>$option['curts']){
                return $getData['id'];
            }else{
                return null;
            }
        }else{
            return null;
        }
    }


    /**
     * 编译条件对象
     */
    protected function compileCondition($queryStruct){
        $condition = array();
        if(isset($queryStruct['conditionSpec']) && !empty($queryStruct['conditionSpec'])){
            $condition = $queryStruct['conditionSpec'];
        }else{
            if(isset($queryStruct['conditionKey']) && !empty($queryStruct['conditionKey'])){
                foreach($queryStruct['conditionKey'] as $key=>$value){
                    $condition[$key] = $value;
                }
            }
            if(isset($queryStruct['conditionIn']) && !empty($queryStruct['conditionIn'])){
                foreach($queryStruct['conditionIn'] as $key=>$value){
                    if (is_array($value)){
                        $condition[$key] = array('$in'=>$value);
                    }
                }
            }
            if(isset($queryStruct['conditionLike']) && !empty($queryStruct['conditionLike'])){
                foreach($queryStruct['conditionLike'] as $key=>$value){
                    $regex   = new MongoRegex('/'.$value.'/i');
                    $condition[$key] = $regex;
                }
            }
        }
        return $condition;
    }


    public function queryRow($queryStruct=array(),$option = array(), $vendorInstance = null){
        $option += array(
            'routeKey'=>$this->getRouteKey(),
            'dbName'=>$this->getDbName(),
            'seqName'=> null,
            'collectionName'=>null,
            'options'=>array(),
        );
        empty($option['dbName']) && $option['dbName'] = lcfirst($option['routeKey']);
        empty($option['collectionName']) && $option['collectionName'] = $option['dbName'];
        empty($option['seqName']) && $option['seqName'] = lcfirst($option['routeKey']);
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            $this->buildRouteAttrCallbackByQueryStruct($queryStruct,$option)
        );

        $collection = $vendorInstance->selectCollection($option['dbName'],$option['collectionName']);

        // 拼接 查询字段
        $queryField = array();
        !array_key_exists('queryField', $queryStruct) && $queryStruct['queryField'] = array('*');
        if(count($queryStruct['queryField'])==1 && $queryStruct['queryField'][0]=='*'){
            $queryField = array();
        }else{
            $queryFiled = array();
            foreach ($queryStruct['queryField'] as $field) {
                $queryFiled[$field]=1;
            }
        }

        // 查询条件
        $queryCondition = $this->compileCondition($queryStruct);
        $retObject = $collection->findOne($queryCondition,$queryField);
        return $retObject;
    }


    public function queryAssoc($queryStruct=array(),$option = array(), $vendorInstance = null){
        $option += array(
            'routeKey'=>$this->getRouteKey(),
            'dbName'=>$this->getDbName(),
            'seqName'=> null,
            'collectionName'=>null,
            'options'=>array(),
            'explain'=>false,
        );
        empty($option['dbName']) && $option['dbName'] = lcfirst($option['routeKey']);
        empty($option['collectionName']) && $option['collectionName'] = $option['dbName'];
        empty($option['seqName']) && $option['seqName'] = lcfirst($option['routeKey']);
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            $this->buildRouteAttrCallbackByQueryStruct($queryStruct,$option)
        );

        $collection = $vendorInstance->selectCollection($option['dbName'],$option['collectionName']);
        // 拼接 查询字段
        $queryField = array();
        !array_key_exists('queryField', $queryStruct) && $queryStruct['queryField'] = array('*');
        if(count($queryStruct['queryField'])==1 && $queryStruct['queryField'][0]=='*'){
            $queryField = array();
        }else{
            $queryFiled = array();
            foreach ($queryStruct['queryField'] as $field) {
                $queryFiled[$field]=1;
            }
        }

        // 查询条件
        $queryCondition = $this->compileCondition($queryStruct);
        // 排序条件
        $querySort = array();
        if(isset($queryStruct['orderSet']) && !empty($queryStruct['orderSet'])){
            foreach ($queryStruct['orderSet'] as $row=>$set){
                foreach($set as $field=>$direction){
                    $direction = strtoupper(trim($direction));
                    ! in_array($direction, array('ASC', 'DESC')) && $direction = 'ASC';
                    $querySort[$field] = $direction=='ASC'? MongoCollection::ASCENDING : MongoCollection::DESCENDING;
                }
            }
        }
        // Limit 分页条件
        $limit = null;
        $offset = null;
        if(isset($queryStruct['limitOffset']) && !empty($queryStruct['limitOffset'])){
            if(isset($queryStruct['limitOffset']['offset']) && isset($queryStruct['limitOffset']['limit'])){
                $limit = $queryStruct['limitOffset']['limit'];
                $offset = $queryStruct['limitOffset']['offset'];
            }elseif (!isset($queryStruct['limitOffset']['offset']) && isset($queryStruct['limitOffset']['limit'])){
                $limit = $queryStruct['limitOffset']['limit'];
            }
        }

        // 结果集游标
        $resCur = $collection->find($queryCondition, $queryField);
        if(!empty($querySort)){
            $resCur = $resCur->sort($querySort);
        }
        if(!is_null($offset)){
            $resCur = $resCur->skip($offset);
        }
        if(!is_null($limit)){
            $resCur = $resCur->limit($limit);
        }

        if($option['explain']==true){
            return $resCur->explain();
        }

        if(isset($queryStruct['returnType'])){
            if($queryStruct['returnType']== self::RETURNTYPE_CURSOR){
                return $resCur;
            }
        }

        $resAssoc = array();
        foreach ($resCur as $rowObject) {
            $resAssoc[] = $rowObject;
        }
        return $resAssoc;
    }

    public function queryCount($queryStruct=array(),$option = array(), $vendorInstance = null){
        $option += array(
            'routeKey'=>$this->getRouteKey(),
            'dbName'=>$this->getDbName(),
            'seqName'=> null,
            'collectionName'=>null,
            'options'=>array(),
            'explain'=>false,
            'cacheCount'=>false,
        );
        empty($option['dbName']) && $option['dbName'] = lcfirst($option['routeKey']);
        empty($option['collectionName']) && $option['collectionName'] = $option['dbName'];
        empty($option['seqName']) && $option['seqName'] = lcfirst($option['routeKey']);
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            $this->buildRouteAttrCallbackByQueryStruct($queryStruct,$option)
        );

        $collection = $vendorInstance->selectCollection($option['dbName'],$option['collectionName']);

        // 查询条件
        $queryCondition = $this->compileCondition($queryStruct);

        if($option['explain']==true){
            return $collection->find($queryCondition)->explain();
        }
        $count = null;
        if($option['cacheCount']==true){
            !isset($conditionHash) && $conditionHash = Util::getDataHashKey(array($option['dbName'],$option['collectionName'],$queryCondition));
            $count = $this->currentQueryCount($option['collectionName'],$conditionHash);
        }
        if(is_null($count)){
            $count = $collection->find($queryCondition)->count();
        }
        if($option['cacheCount']==true){
            !isset($conditionHash) && $conditionHash = Util::getDataHashKey(array($option['dbName'],$option['collectionName'],$queryCondition));
            $this->setQueryCount($option['collectionName'],$conditionHash,$count);
        }
        return $count;
    }

    public function queryStruct($queryStruct=array(),$option = array(), $vendorInstance = null){
        $option += array(
            'routeKey'=>$this->getRouteKey(),
            'dbName'=>$this->getDbName(),
            'seqName'=> null,
            'collectionName'=>null,
            'options'=>array(),
            'explain'=>false,
            'cacheCount'=>false,
        );
        empty($option['dbName']) && $option['dbName'] = lcfirst($option['routeKey']);
        empty($option['collectionName']) && $option['collectionName'] = $option['dbName'];
        empty($option['seqName']) && $option['seqName'] = lcfirst($option['routeKey']);
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            $this->buildRouteAttrCallbackByQueryStruct($queryStruct,$option)
        );

        $collection = $vendorInstance->selectCollection($option['dbName'],$option['collectionName']);

        // 拼接 查询字段
        $queryField = array();
        !array_key_exists('queryField', $queryStruct) && $queryStruct['queryField'] = array('*');
        if(count($queryStruct['queryField'])==1 && $queryStruct['queryField'][0]=='*'){
            $queryField = array();
        }else{
            $queryFiled = array();
            foreach ($queryStruct['queryField'] as $field) {
                $queryFiled[$field]=TRUE;
            }
        }

        // 查询条件
        $queryCondition = $this->compileCondition($queryStruct);
        // 排序条件
        $querySort = array();
        if(isset($queryStruct['orderSet']) && !empty($queryStruct['orderSet'])){
            foreach ($queryStruct['orderSet'] as $row=>$set){
                foreach($set as $field=>$direction){
                    $direction = strtoupper(trim($direction));
                    ! in_array($direction, array('ASC', 'DESC')) && $direction = 'ASC';
                    $querySort[$field] = $direction=='ASC'? MongoCollection::ASCENDING : MongoCollection::DESCENDING;
                }
            }
        }
        // Limit 分页条件
        $limit = null;
        $offset = null;
        if(isset($queryStruct['limitOffset']) && !empty($queryStruct['limitOffset'])){
            if(isset($queryStruct['limitOffset']['offset']) && isset($queryStruct['limitOffset']['limit'])){
                $limit = $queryStruct['limitOffset']['limit'];
                $offset = $queryStruct['limitOffset']['offset'];
            }elseif (!isset($queryStruct['limitOffset']['offset']) && isset($queryStruct['limitOffset']['limit'])){
                $limit = $queryStruct['limitOffset']['limit'];
            }
        }
        // 结果集游标
        $resCur = $collection->find($queryCondition, $queryField);
        //var_export($resCur);exit;
        if(!empty($querySort)){
            $resCur = $resCur->sort($querySort);
        }
        if(!is_null($offset)){
            $resCur = $resCur->skip($offset);
        }
        if(!is_null($limit)){
            $resCur = $resCur->limit($limit);
        }

        if($option['explain']==true){
            return $resCur->explain();
        }
        if(isset($queryStruct['returnType'])){
            if($queryStruct['returnType']== self::RETURNTYPE_CURSOR){
                return $resCur;
            }
        }
        $resCount = null;
        if($option['cacheCount']==true){
            !isset($conditionHash) && $conditionHash = Util::getDataHashKey(array($option['dbName'],$option['collection'],$queryCondition));
            $resCount = $this->currentQuerycount($option['collection'],$conditionHash);
        }
        if(is_null($resCount)){
            $resCount = $resCur->count();
        }
        if($option['cacheCount']==true){
            !isset($conditionHash) && $conditionHash = Util::getDataHashKey(array($option['dbName'],$option['collection'],$queryCondition));
            $this->setQueryCount($option['collection'],$conditionHash,$resCount);
        }

        $resAssoc = array();
        if($resCount>0){
            foreach ($resCur as $rowObject) {
                $resAssoc[] = $rowObject;
            }
        }
        $retStruct = array(
            'count'=>$resCount,
            'assoc'=>$resAssoc
        );
        return $retStruct;
    }


    public function queryDelete($queryStruct=array(),$option = array(), $vendorInstance = null){
        $option += array(
            'routeKey'=>$this->getRouteKey(),
            'dbName'=>$this->getDbName(),
            'seqName'=> null,
            'collectionName'=>null,
            'options'=>array(),
        );
        empty($option['dbName']) && $option['dbName'] = lcfirst($option['routeKey']);
        empty($option['collectionName']) && $option['collectionName'] = $option['dbName'];
        empty($option['seqName']) && $option['seqName'] = lcfirst($option['routeKey']);
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            $this->buildRouteAttrCallbackByQueryStruct($queryStruct,$option)
        );

        $collection = $vendorInstance->selectCollection($option['dbName'],$option['collectionName']);

        // 查询条件
        $queryCondition = $this->compileCondition($queryStruct);
        return $collection->remove($queryCondition,$option['options']);
    }



    public function queryUpdate($queryStruct=array(),$setData=array(),$option = array(), $vendorInstance = null){
        $option += array(
            'routeKey'=>$this->getRouteKey(),
            'dbName'=>$this->getDbName(),
            'seqName'=> null,
            'collectionName'=>null,
            'options'=>array(),
        );
        !isset($option['options']['safe']) && $option['options']['safe']=true;
        !isset($option['options']['multiple']) && $option['options']['multiple']=true;
        empty($option['dbName']) && $option['dbName'] = lcfirst($option['routeKey']);
        empty($option['collectionName']) && $option['collectionName'] = $option['dbName'];
        empty($option['seqName']) && $option['seqName'] = lcfirst($option['routeKey']);
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            $this->buildRouteAttrCallbackByQueryStruct($queryStruct,$option)
        );

        $collection = $vendorInstance->selectCollection($option['dbName'],$option['collectionName']);

        // 查询条件
        $queryCondition = $this->compileCondition($queryStruct);
        return $collection->update($queryCondition,array('$set' => $setData),$option['options']);
    }



    public function create($requestData=array(),$option = array(), $vendorInstance = null){
        $option += array(
            'routeKey'=>$this->getRouteKey(),
            'dbName'=>$this->getDbName(),
            'seqName'=> null,
            'seqOption'=>array(),
            'collectionName'=>null,
            'options'=>array(),
            'idLabel'=>'id',
        );
        empty($option['dbName']) && $option['dbName'] = lcfirst($option['routeKey']);
        empty($option['collectionName']) && $option['collectionName'] = $option['dbName'];
        empty($option['seqName']) && $option['seqName'] = lcfirst($option['routeKey']);

        $id = null;
        if(!empty($option['idLabel'])){
            if(!isset($requestData[$option['idLabel']])){
                $requestData[$option['idLabel']] = $this->nextSeq($option['seqName'],$option['seqOption']);
            }
            $id = $requestData[$option['idLabel']];
        }

        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            $this->buildRouteAttrCallbackByDict($requestData,$option)
        );

        $collection = $vendorInstance->selectCollection($option['dbName'],$option['collectionName']);

        $resultStatus = $collection->insert($requestData,$option['options']);

        return array('status'=>$resultStatus,'id'=>$id);
    }


    public function read($requestData=array(),$option = array(), $vendorInstance = null){
        $option += array(
            'routeKey'=>$this->getRouteKey(),
            'dbName'=>$this->getDbName(),
            'seqName'=> null,
            'seqOption'=>array(),
            'collectionName'=>null,
            'options'=>array(),
            'idLabel'=>'id',
        );
        empty($option['dbName']) && $option['dbName'] = lcfirst($option['routeKey']);
        empty($option['collectionName']) && $option['collectionName'] = $option['dbName'];
        empty($option['seqName']) && $option['seqName'] = lcfirst($option['routeKey']);

        $id = null;
        if(!empty($option['idLabel'])){
            if(isset($requestData[$option['idLabel']])){
                $id = $requestData[$option['idLabel']];
            }
        }

        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            $this->buildRouteAttrCallbackByDict($requestData,$option)
        );

        $collection = $vendorInstance->selectCollection($option['dbName'],$option['collectionName']);

        $getData = null;
        if(isset($requestData[$option['idLabel']])){
            $getData = $collection->findOne(array($option['idLabel'] => $requestData[$option['idLabel']]));
        }
        return $getData;
    }


    public function update($requestData=array(),$option = array(), $vendorInstance = null){
        $option += array(
            'routeKey'=>$this->getRouteKey(),
            'dbName'=>$this->getDbName(),
            'seqName'=> null,
            'seqOption'=>array(),
            'collectionName'=>null,
            'options'=>array(),
            'idLabel'=>'id',
        );
        !isset($option['options']['safe']) && $option['options']['safe']=true;
        empty($option['dbName']) && $option['dbName'] = lcfirst($option['routeKey']);
        empty($option['collectionName']) && $option['collectionName'] = $option['dbName'];
        empty($option['seqName']) && $option['seqName'] = lcfirst($option['routeKey']);

        $id = null;
        if(!empty($option['idLabel'])){
            if(isset($requestData[$option['idLabel']])){
                $id = $requestData[$option['idLabel']];
            }
        }

        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            $this->buildRouteAttrCallbackByDict($requestData,$option)
        );

        $collection = $vendorInstance->selectCollection($option['dbName'],$option['collectionName']);

        $getData = null;
        $updateStatus = null;
        $gotStatus = false;
        if(isset($requestData[$option['idLabel']])){
            $getData = $collection->findOne(array($option['idLabel'] => $requestData[$option['idLabel']]));
        }
        if(!empty($getData)){
            $gotStatus = true;
            $setData = array();
            foreach ($requestData as $key=>$val) {
                $key!=$option['idLabel'] && array_key_exists($key,$getData) && $getData[$key]!==$val && $setData[$key] = $val;
            }
            if(!empty($setData)){
                $updateStatus = $collection->update(array($option['idLabel'] => $requestData[$option['idLabel']]), array('$set' => $setData),$option['options']);
            }
        }
        return array('status'=>$updateStatus,'got'=>$gotStatus);
    }


    public function replace($requestData=array(),$option = array(), $vendorInstance = null){
        $option += array(
            'routeKey'=>$this->getRouteKey(),
            'dbName'=>$this->getDbName(),
            'seqName'=> null,
            'seqOption'=>array(),
            'collectionName'=>null,
            'options'=>array(),
            'idLabel'=>'id',
        );
        !isset($option['options']['safe']) && $option['options']['safe']=true;
        empty($option['dbName']) && $option['dbName'] = lcfirst($option['routeKey']);
        empty($option['collectionName']) && $option['collectionName'] = $option['dbName'];
        empty($option['seqName']) && $option['seqName'] = lcfirst($option['routeKey']);

        $id = null;
        if(!empty($option['idLabel'])){
            if(isset($requestData[$option['idLabel']])){
                $id = $requestData[$option['idLabel']];
            }
        }

        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            $this->buildRouteAttrCallbackByDict($requestData,$option)
        );

        $collection = $vendorInstance->selectCollection($option['dbName'],$option['collectionName']);

        $getData = null;
        $updateStatus = null;
        $gotStatus = false;
        if(isset($requestData[$option['idLabel']])){
            $getData = $collection->findOne(array($option['idLabel'] => $requestData[$option['idLabel']]));
        }
        if(!empty($getData)){
            $gotStatus = true;
            $setData = $requestData;
            if(!empty($setData)){
                $updateStatus = $collection->update(array($option['idLabel'] => $requestData[$option['idLabel']]), $setData,$option['options']);
            }
        }
        return array('status'=>$updateStatus,'got'=>$gotStatus);
    }


    public function delete($requestData=array(),$option = array(), $vendorInstance = null){
        $option += array(
            'routeKey'=>$this->getRouteKey(),
            'dbName'=>$this->getDbName(),
            'seqName'=> null,
            'seqOption'=>array(),
            'collectionName'=>null,
            'options'=>array(),
            'idLabel'=>'id',
        );
        !isset($option['options']['safe']) && $option['options']['safe']=true;
        empty($option['dbName']) && $option['dbName'] = lcfirst($option['routeKey']);
        empty($option['collectionName']) && $option['collectionName'] = $option['dbName'];
        empty($option['seqName']) && $option['seqName'] = lcfirst($option['routeKey']);

        $id = null;
        if(!empty($option['idLabel'])){
            if(isset($requestData[$option['idLabel']])){
                $id = $requestData[$option['idLabel']];
            }
        }

        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            $this->buildRouteAttrCallbackByDict($requestData,$option)
        );

        $collection = $vendorInstance->selectCollection($option['dbName'],$option['collectionName']);

        $getData = null;
        $updateStatus = null;
        $gotStatus = false;
        if(isset($requestData[$option['idLabel']])){
            $getData = $collection->findOne(array($option['idLabel'] => $requestData[$option['idLabel']]));
        }
        if(!empty($getData)){
            $gotStatus = true;
            $updateStatus = $collection->remove(array($option['idLabel'] => $requestData[$option['idLabel']]),$option['options']);
        }
        return array('status'=>$updateStatus,'got'=>$gotStatus);
    }


    public function upsert($requestData=array(),$option = array(), $vendorInstance = null){
        $option += array(
            'routeKey'=>$this->getRouteKey(),
            'dbName'=>$this->getDbName(),
            'seqName'=> null,
            'seqOption'=>array(),
            'collectionName'=>null,
            'options'=>array(),
            'idLabel'=>'id',
        );
        !isset($option['options']['safe']) && $option['options']['safe']=true;
        !isset($option['options']['upsert']) && $option['options']['upsert']=true;
        empty($option['dbName']) && $option['dbName'] = lcfirst($option['routeKey']);
        empty($option['collectionName']) && $option['collectionName'] = $option['dbName'];
        empty($option['seqName']) && $option['seqName'] = lcfirst($option['routeKey']);

        $id = null;
        $findBool = true;
        if(!empty($option['idLabel'])){
            if(!isset($requestData[$option['idLabel']])){
                $findBool = false;
                $requestData[$option['idLabel']] = $this->nextSeq($option['seqName'],$option['seqOption']);
            }
            $id = $requestData[$option['idLabel']];
        }

        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            $this->buildRouteAttrCallbackByDict($requestData,$option)
        );

        $collection = $vendorInstance->selectCollection($option['dbName'],$option['collectionName']);

        $getData = null;
        $updateStatus = null;
        $gotStatus = false;

        if($findBool==true){
            if(isset($requestData[$option['idLabel']])){
                $getData = $collection->findOne(array($option['idLabel'] => $requestData[$option['idLabel']]));
            }
            if(!empty($getData)){
                $gotStatus = true;
                $setData = $requestData;
                if(!empty($setData)){
                    $updateStatus = $collection->update(array($option['idLabel'] => $requestData[$option['idLabel']]), array('$set' => $setData),$option['options']);
                }
            }
        }else{
            $updateStatus = $collection->insert($requestData,$option['options']);
        }
        return array('status'=>$updateStatus,'got'=>$gotStatus);
    }


}