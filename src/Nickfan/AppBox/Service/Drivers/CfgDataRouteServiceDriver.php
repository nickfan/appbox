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


use Nickfan\AppBox\Common\Exception\RuntimeException;
use Nickfan\AppBox\Config\DataRouteConf;
use Nickfan\AppBox\Instance\DataRouteInstance;
use Nickfan\AppBox\Service\BaseDataRouteServiceDriver;
use Nickfan\AppBox\Service\DataRouteServiceDriverInterface;
use Nickfan\AppBox\Instance\DataRouteInstanceDriverInterface;
use Closure;
class CfgDataRouteServiceDriver extends BaseDataRouteServiceDriver implements DataRouteServiceDriverInterface{

    protected static $driverKey = 'cfg';

    public function getById($id=0,$option=array(),$driverInstance=null){
        $option+=array(
        );
        $driverInstance = $this->getSerivceInstance($option,$driverInstance,array('id'=>$id,));
        return isset($driverInstance->$id)?$driverInstance->$id:null;
    }
} 