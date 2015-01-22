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

require_once __DIR__ . '/../../../../bootstrap/initenv.php';

use Nickfan\AppBox\Config\BoxRouteConf;
use Nickfan\AppBox\Common\Usercache\ApcBoxUsercache;

$instBoxRouteConf = new BoxRouteConf($app['path.storage'].'/etc/local',new ApcBoxUsercache());


$clearResult = $instBoxRouteConf->cacheFlush();var_dump($clearResult);

$routeConfKeys = $instBoxRouteConf->getRouteConfSubKeys('redis','mygroup');
var_dump($routeConfKeys);
$routeConf = $instBoxRouteConf->getRouteConfByScript('redis','mygroup',array('id'=>3));
var_dump($routeConf);

