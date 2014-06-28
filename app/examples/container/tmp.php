<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-25 17:01
 *
 */

require_once __DIR__.'/../../bootstrap/bootstrap.php';

use Nickfan\AppBox\Support\Facades\Config;
use Nickfan\AppBox\Support\Facades\DataRouteConf;
use Nickfan\AppBox\Support\Facades\DataRouteInstance;

$confDict = Config::get('common.itemPerPages');
//var_dump($confDict);
$clearResult = DataRouteConf::cacheFlush();
//var_dump($clearResult);
$routeConf = DataRouteConf::getRouteConfByScript('redis', 'mygroup', array('id' => 3));
//var_dump($routeConf);

$dataRouteInstance = DataRouteInstance::getRouteInstance('redis','mygroup',array('id'=>3));
$serviceInstance = $dataRouteInstance->getInstance();
//var_dump($dataRouteInstance);
$serviceInstance->setex('abc',5,'123');
$res = $serviceInstance->get('abc');
var_dump($res);


