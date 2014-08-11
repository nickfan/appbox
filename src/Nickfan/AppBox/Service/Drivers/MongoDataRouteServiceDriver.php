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

class MongoDataRouteServiceDriver extends BaseDataRouteServiceDriver implements DataRouteServiceDriverInterface {

    //protected static $driverKey = 'mongo';

    protected $dbName = '';

    public function __init($params=array()){
        $params+=array(
            'routeKey'=>AppConstants::CONF_KEY_ROOT,
            'dbName'=>null,
        );
        $this->setRouteKey($params['routeKey']);
        if(empty($params['dbName'])){
            $dbName = lcfirst($params['routeKey']);
            $this->setDbName($dbName);
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

}