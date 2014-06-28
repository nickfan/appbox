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

require_once __DIR__.'/../../../bootstrap/initenv.php';

use Nickfan\AppBox\Common\Usercache\ApcUsercache;
use Nickfan\AppBox\Config\DataRouteConf;
use Nickfan\AppBox\Instance\DataRouteInstance;

$instDataRouteInstance = DataRouteInstance::getInstance(new DataRouteConf(new ApcUsercache(),$app['path.storage'].'/etc/local'));


$routeInstance = $instDataRouteInstance->getRouteInstance('cfg','mygroup',array('id'=>3));
var_dump($routeInstance);
$instance = $routeInstance->getInstance();
var_dump($instance);
