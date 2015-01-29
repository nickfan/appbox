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


class PredisBoxRouteInstanceDriver extends BoxBaseRouteInstanceDriver implements BoxRouteInstanceDriverInterface {

    /**
     * do driver instance init
     */
    public function setup() {
        $settings = $this->getSettings();
        if (empty($settings)) {
            throw new BoxRouteInstanceException('init driver instance failed: empty settings');
        }

        $curInst = null;

        if(empty($settings['options'])){
            $curInst = new \Predis\Client($settings['parameters']);
        }else{
            $curInst = new \Predis\Client($settings['parameters'],$settings['options']);
        }

        $this->instance = $curInst;
        //$this->isAvailable = $this->instance ? true : false;
        $this->isAvailable = true;
    }

    public function close() {
        try {
            if($this->instance){
                unset($this->instance);
            }
        } catch (\Exception $ex) {
        }
        $this->isAvailable = false;
    }
}
