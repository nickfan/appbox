<?php
/**
 * Description
 *
 * @project appbox
 * @package 
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-11 11:14
 *
 */



namespace Nickfan\MyApp\Package\Index;

use Nickfan\AppBox\Support\Facades\Config;
use Nickfan\AppBox\Support\Facades\DataRouteConf;
use Nickfan\AppBox\Support\Facades\DataRouteInstance;

class Index {

    protected $dispatcher;
    public function __construct($dispatcherInstance){
        $this->dispatcher = $dispatcherInstance;
    }
    public function Index(){
        echo 'helloworld';
        $confDict = Config::get('common.itemPerPages');
        var_dump($confDict);
        $clearResult = DataRouteConf::cacheFlush();
        var_dump($clearResult);
        $routeConf = DataRouteConf::getRouteConfByScript('redis','mygroup',array('id'=>3));
        var_dump($routeConf);
        $routeInstance = DataRouteInstance::getRouteInstance('cfg','mygroup',array('id'=>3));
        var_dump($routeInstance);
        $instance = $routeInstance->getInstance();
        var_dump($instance);
    }
    public function Ping(){
        echo 'pong';
        return 9527;
    }
} 