<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-27 16:31
 *
 */

namespace Nickfan\BoxApp\BoxCommand\LocalhostColon8000\Index;

use Nickfan\AppBox\Support\Facades\AppBox;
use Nickfan\BoxApp\BoxController\BoxAbstractController;

class Index extends BoxAbstractController {

    public function Index(){
        echo 'helloworld';
        $confDict = AppBox::make('conf')->get('common.itemPerPages');
        var_dump($confDict);
        $clearResult = AppBox::make('routeconf')->cacheFlush();
        var_dump($clearResult);
        $routeConf = AppBox::make('routeconf')->getRouteConfByScript('redis','mygroup',array('id'=>3));
        var_dump($routeConf);
        $routeInstance = AppBox::make('routeinst')->getRouteInstance('cfg','mygroup',array('id'=>3));
        var_dump($routeInstance);
        $instance = $routeInstance->getInstance();
        var_dump($instance);
        $getCurrentUri = $this->dispatcher->getCurrentUri();
        var_dump($getCurrentUri);
    }
    public function Ping(){
        echo 'pong';
        return 9527;
    }
}