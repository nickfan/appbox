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

use Nickfan\AppBox\Common\Usercache\ApcBoxUsercache;
use Nickfan\AppBox\Config\BoxRouteConf;
use Nickfan\AppBox\Instance\BoxRouteInstance;

$instBoxRouteInstance = BoxRouteInstance::getInstance(new BoxRouteConf($boxapp['path.storage'].'/etc/local',new ApcBoxUsercache()));


$routeInstance = $instBoxRouteInstance->getRouteInstance('cfg','mygroup',array('id'=>3));
var_dump($routeInstance);
$instance = $routeInstance->getInstance();
var_dump($instance);
