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

class GearmanWorkerBoxRouteInstanceDriver extends BoxBaseRouteInstanceDriver implements BoxRouteInstanceDriverInterface {

    /**
     * do driver instance init
     */
    public function setup() {
        $settings = $this->getSettings();
        if (empty($settings)) {
            throw new BoxRouteInstanceException('init driver instance failed: empty settings');
        }
        $curInst = new \GearmanWorker();
        $curInst->addServers($settings['gearmanHosts']);

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