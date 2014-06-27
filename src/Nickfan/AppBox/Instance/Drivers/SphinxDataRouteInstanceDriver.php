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

use Nickfan\AppBox\Common\Exception\DataRouteInstanceException;
use Nickfan\AppBox\Instance\BaseDataRouteInstanceDriver;
use Nickfan\AppBox\Instance\DataRouteInstanceDriverInterface;

class SphinxDataRouteInstanceDriver extends BaseDataRouteInstanceDriver implements DataRouteInstanceDriverInterface {

    /**
     * do driver instance init
     */
    public function setup() {
        $settings = $this->getSettings();
        if (empty($settings)) {
            throw new DataRouteInstanceException('init driver instance failed: empty settings');
        }
        $curInst = new \SphinxClient;
        $curInst->setServer($settings['sphinxHost'],$settings['sphinxPort']);
        !empty($settings['sphinxConnectTimeout']) && $curInst->setConnectTimeout($settings['sphinxConnectTimeout']);
        $this->instance = $curInst;
        $this->isAvailable = $this->instance ? true : false;
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