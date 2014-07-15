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

}