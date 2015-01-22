<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-26 11:03
 *
 */


namespace Nickfan\AppBox\Instance\Drivers;

use Nickfan\AppBox\Common\Exception\BoxRouteInstanceException;
use Nickfan\AppBox\Instance\BoxBaseRouteInstanceDriver;
use Nickfan\AppBox\Instance\BoxRouteInstanceDriverInterface;

class MongoBoxRouteInstanceDriver extends BoxBaseRouteInstanceDriver implements BoxRouteInstanceDriverInterface {

    /**
     * do driver instance init
     */
    public function setup() {
        $settings = $this->getSettings();
        if (empty($settings)) {
            throw new BoxRouteInstanceException('init driver instance failed: empty settings');
        }
        $curOptions = array();
        !empty($settings['mongoUser']) && $curOptions['username']=$settings['mongoUser'];
        !empty($settings['mongoPasswd']) && $curOptions['password']=$settings['mongoPasswd'];
        !empty($settings['mongoReplicaSet']) && $curOptions['replicaSet']=$settings['mongoReplicaSet'];
        $curHost = $settings['mongoHost'];
        // 新版驱动不支持
        //$curOptions['persist'] = $curHost;
        $curConnStr = 'mongodb://'.$curHost;
        if(!empty($settings['mongoConnSets'])){
            $curConnStr = rtrim($curConnStr,'/').'/';
            $curConnStr.= $settings['mongoConnSets'];
        }
        if(!empty($curOptions)){
            $curInst = new \Mongo($curConnStr,$curOptions);
        }else{
            $curInst = new \Mongo($curConnStr);
        }
        if(isset($settings['mongoSetSlaveOk']) && $settings['mongoSetSlaveOk']==1){
            /*
             * DEPRECATED
             */
            /*
            //$curInst->setSlaveOkay(TRUE);
            */
        }
        $this->instance = $curInst;
        //$this->isAvailable = $this->instance ? true : false;
        $this->isAvailable = true;
    }

    public function close() {
        try {
            if($this->instance){
                $this->instance->close();
            }
        } catch (\Exception $ex) {
        }
        $this->isAvailable = false;
    }
}