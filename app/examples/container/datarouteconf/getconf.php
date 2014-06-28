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

use Nickfan\AppBox\Support\Facades\DataRouteConf;


$clearResult = DataRouteConf::cacheFlush();var_dump($clearResult);

$routeConfKeys = DataRouteConf::getRouteConfSubKeys('redis','mygroup');
var_dump($routeConfKeys);
$routeConf = DataRouteConf::getRouteConfByScript('redis','mygroup',array('id'=>3));
var_dump($routeConf);
