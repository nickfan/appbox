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
use Pheanstalk\Pheanstalk;

class BeanstalkBoxRouteInstanceDriver extends BoxBaseRouteInstanceDriver implements BoxRouteInstanceDriverInterface {

    /**
     * do driver instance init
     */
    public function setup() {
        $settings = $this->getSettings();
        if (empty($settings)) {
            throw new BoxRouteInstanceException('init driver instance failed: empty settings');
        }
        $bstHostsArr = explode(',', rtrim($settings['beanstalkHosts'], ','));

        $bstHost = array_shift($bstHostsArr);
        $curBstHostInfo = explode(':', $bstHost);
        $curInst = new Pheanstalk($curBstHostInfo[0], isset($curBstHostInfo[1]) ? $curBstHostInfo[1] : 11300);
        // sf.net的beanstalk版本 文件版初始化
//        $curInst = BeanStalk::open(array(
//        		'servers'       => $bstHostsArr,
//             //'select'                => 'random wait',
//             //'connection_timeout'    => 0.5,
//             //'peek_usleep'           => 2500,
//             //'connection_retries'    => 3,
//             //'auto_unyaml'           => true
//        ));
        // 插件版初始化
//        $curInst = new Beanstalk();
//        foreach($bstHostsArr as $bstHost){
// 			$curBstHostInfo=explode(':',$bstHost);
// 			$curInst->addServer($curBstHostInfo[0],isset($curBstHostInfo[1])?$curBstHostInfo[1]:11300,1);
//        }
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