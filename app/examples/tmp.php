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

require_once '../bootstrap/bootstrap.php';

use Nickfan\AppBox\Support\Facades\Config;
use Nickfan\AppBox\Support\Facades\DataRouteConf;

$confDict = Config::get('common.itemPerPages');
var_dump($confDict);
$clearResult = DataRouteConf::cacheFlush();
var_dump($clearResult);
$routeConf = DataRouteConf::getRouteConfByScript('redis', 'mygroup', array('id' => 3));
var_dump($routeConf);
