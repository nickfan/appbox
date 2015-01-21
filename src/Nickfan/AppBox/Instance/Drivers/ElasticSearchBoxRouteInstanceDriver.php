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

class ElasticSearchBoxRouteInstanceDriver extends BoxBaseRouteInstanceDriver implements BoxRouteInstanceDriverInterface {

    /**
     * do driver instance init
     */
    public function setup() {
        $settings = $this->getSettings();
        if (empty($settings)) {
            throw new BoxRouteInstanceException('init driver instance failed: empty settings');
        }
        $esHostsList = explode(',', rtrim($settings['esHosts'], ','));

        $hostsInfo = array();
        foreach ($esHostsList as $rowEsHostStr) {
            $rowEsHostArr = explode(':',$rowEsHostStr);
            $rowEsHostDomain = $rowEsHostArr[0];
            $rowEsHostPort = isset($rowEsHostArr[1])?$rowEsHostArr[1]:9200;
            $hostsInfo[] = array(
                'host'=>$rowEsHostDomain,
                'port'=>$rowEsHostPort,
            );
        }
        if(count($hostsInfo)>1){
            $curInst = new \Elastica\Client(array('servers'=>$hostsInfo));
        }else{
            $curInst =  new \Elastica\Client($hostsInfo[0]);
        }
//        $params = array();
//        $params['hosts'] = $esHostsList;
//        $curInst = new \Elasticsearch\Client($params);
        $this->instance = $curInst;
        $this->isAvailable = $this->instance ? true : false;
    }

    public function close() {
        try {
            if($this->instance){
                unset($this->instance);
                $this->instance = null;
            }
        } catch (\Exception $ex) {
        }
        $this->isAvailable = false;
    }
}