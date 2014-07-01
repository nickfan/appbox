<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-05 14:38
 *
 */

require_once __DIR__.'/../../../bootstrap/bootstrap.php';

use Nickfan\AppBox\Support\Facades\DataRouteInstance;
use Nickfan\AppBox\Service\Drivers\CfgDataRouteServiceDriver;
use Nickfan\AppBox\Service\Drivers\RedisDataRouteServiceDriver;

$instDataRouteInstance = DataRouteInstance::getStaticInstance();
$cfgService = CfgDataRouteServiceDriver::factory(
    $instDataRouteInstance
);

$key = 'dsn';
$optionEg1 = array(
    'routeKey' => 'root',
    //'routeMode' => DataRouteInstance::DATAROUTE_MODE_ATTR,
);
$optionEg2 = array(
    'routeKey' => 'mygroup',
    'routeMode' => DataRouteInstance::DATAROUTE_MODE_ATTR,
    'routeAttr' => array('id'=>3),
);

$optionEg3 = array(
    'routeMode' => DataRouteInstance::DATAROUTE_MODE_IDSET,
    'routeIdSet' => array('routeKey'=>'mygroup','group'=>'g1'),
);

$optionEg4 = array(
    'routeMode' => DataRouteInstance::DATAROUTE_MODE_DIRECT,
    'routeSettings' => array(
        'dsn'=>'ftp://myuser:mypass@myhost/mypath?arg1=v1&arg2=v2#anchor1=a1&anchor2=a2',
        'timeout'=>30,
        'settings'=>'',
    ),
);

$data1 = $cfgService->getByKey($key, $optionEg1);
var_dump($data1);

$data2 = $cfgService->getByKey($key, $optionEg2);
var_dump($data2);

$data3 = $cfgService->getByKey($key, $optionEg3);
var_dump($data3);

$data4 = $cfgService->getByKey($key, $optionEg4);
var_dump($data4);

$redisService = RedisDataRouteServiceDriver::factory($instDataRouteInstance);
// \Nickfan\AppBox\Foundation\AppBox::debug(true,$redisService);  //exit; // [DEV-DEBUG]---
$mykey='abc';
$myval='def';
$myoption=array(
    'routeKey'=>'mygroup',
);
$setResult = $redisService->set($mykey,$myval,$myoption);
var_dump($setResult);
$getResult = $redisService->get($mykey);
var_dump($getResult);
