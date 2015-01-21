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

namespace Nickfan\BoxApp\Controller\MydevDotcom\Index;

use Nickfan\AppBox\Support\Facades\Config;
use Nickfan\AppBox\Support\Facades\DataRouteConf;
use Nickfan\AppBox\Support\Facades\DataRouteInstance;
use Nickfan\BoxApp\Controller\AbstractController;
class Index extends AbstractController{

    public function Index(){
        echo 'helloworld';
        $confDict = Config::get('common.itemPerPages');
        var_dump($confDict);
        //$clearResult = BoxRouteConf::cacheFlush();var_dump($clearResult);
        $routeConfKeys = DataRouteConf::getRouteConfSubKeys('redis','mygroup');
        var_dump($routeConfKeys);
        $routeConf = DataRouteConf::getRouteConfByScript('redis','mygroup',array('id'=>3));
        var_dump($routeConf);
        $routeInstance = DataRouteInstance::getRouteInstance('cfg','mygroup',array('id'=>3));
        var_dump($routeInstance);
        $instance = $routeInstance->getInstance();
        var_dump($instance);
        $getCurrentUri = $this->dispatcher->getCurrentUri();
        var_dump($getCurrentUri);
    }
    public function Ping($myvar1=0,$myvar2='abc'){
        echo 'pong'.'<br/>';
        echo $myvar1.'<br/>';
        echo $myvar2.'<br/>';
        return 9527;
    }
}