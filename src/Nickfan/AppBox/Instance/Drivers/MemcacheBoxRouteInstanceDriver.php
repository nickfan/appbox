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

class MemcacheBoxRouteInstanceDriver extends BoxBaseRouteInstanceDriver implements BoxRouteInstanceDriverInterface {

    /**
     * do driver instance init
     */
    public function setup() {
        $settings = $this->getSettings();
        if (empty($settings)) {
            throw new BoxRouteInstanceException('init driver instance failed: empty settings');
        }
        $memHostsArr=explode(',',rtrim($settings['memHosts'],','));
        if (class_exists('Memcached')) {
            $curInst=new \Memcached();
        }else{
            //$curInst=new Memcache();
            throw new BoxRouteInstanceException('Instance init failed:extension [memcached] required. ');
        }
        foreach($memHostsArr as $memHost){
            $curMemHostInfo=explode(':',$memHost);
            $curInst->addServer($curMemHostInfo[0],isset($curMemHostInfo[1])?$curMemHostInfo[1]:11211);
        }
        $this->instance = $curInst;
        $this->isAvailable = $this->instance ? true : false;
    }

    public function close() {
        try {
            if($this->instance){
                $this->instance->quit();
//                if (class_exists('Memcached')) {
//                    $this->instance->quit();
//                }else{
//                    $this->instance->close();
//                }
            }
        } catch (\Exception $ex) {
        }
        $this->isAvailable = false;
    }
}