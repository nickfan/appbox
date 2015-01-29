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

namespace App\Boxcontrollers\Localhost\Index;

use Nickfan\BoxApp\BoxController\BoxAbstractController;

use Nickfan\BoxApp\Support\Facades\BoxDispatcher;
use Nickfan\AppBox\Support\Facades\BoxConf;
use Nickfan\AppBox\Support\Facades\BoxRouteConf;
use Nickfan\AppBox\Support\Facades\BoxRouteInst;

class Index extends BoxAbstractController {

    public function Index(){
        echo 'helloworld';
        $confDict = BoxConf::get('common.itemPerPages');
        var_dump($confDict);
        $clearResult = BoxRouteConf::cacheFlush();
        var_dump($clearResult);
        $routeConf = BoxRouteConf::getRouteConfByScript('redis','mygroup',array('id'=>3));
        var_dump($routeConf);
        $routeInstance = BoxRouteInst::getRouteInstance('cfg','mygroup',array('id'=>3));
        var_dump($routeInstance);
        $instance = $routeInstance->getInstance();
        var_dump($instance);
        $getCurrentUri = BoxDispatcher::getCurrentUri();
        var_dump($getCurrentUri);
    }
    public function Ping(){
        echo 'pong';
        return 9527;
    }
}
