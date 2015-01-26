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

namespace Nickfan\BoxApp\BoxController\Localhost\Index;

use Nickfan\BoxApp\BoxController\BoxAbstractController;
use Nickfan\AppBox\Support\Facades\AppBox;
use Nickfan\BoxApp\Support\Facades\BoxDispatcher;

class Index extends BoxAbstractController {

    public function Index(){
        echo 'helloworld';
        $confDict = AppBox::make('boxconf')->get('common.itemPerPages');
        var_dump($confDict);
        $clearResult = AppBox::make('boxrouteconf')->cacheFlush();
        var_dump($clearResult);
        $routeConf = AppBox::make('boxrouteconf')->getRouteConfByScript('redis','mygroup',array('id'=>3));
        var_dump($routeConf);
        $routeInstance = AppBox::make('boxrouteinst')->getRouteInstance('cfg','mygroup',array('id'=>3));
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
