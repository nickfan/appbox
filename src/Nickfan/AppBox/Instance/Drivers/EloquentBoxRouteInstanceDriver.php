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

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;

class EloquentBoxRouteInstanceDriver extends BoxBaseRouteInstanceDriver implements BoxRouteInstanceDriverInterface {

    /**
     * do driver instance init
     */
    public function setup() {
        $settings = $this->getSettings();
        if (empty($settings)) {
            throw new BoxRouteInstanceException('init driver instance failed: empty settings');
        }

        $curInst = null;


        $curInst = new Capsule;

        // 创建链接
        $curInst->addConnection($settings);

        // 设置全局静态可访问
        //$curInst->setAsGlobal();

        // 启动Eloquent
        $curInst->bootEloquent();

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
