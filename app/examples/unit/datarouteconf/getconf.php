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

use Nickfan\AppBox\Config\BoxRouteConf;
use Nickfan\AppBox\Common\Usercache\ApcBoxBaseUsercache;

$instDataRouteConf = new BoxRouteConf(new ApcBoxBaseUsercache(),$app['path.storage'].'/etc/local');


$clearResult = $instDataRouteConf->cacheFlush();var_dump($clearResult);

$routeConfKeys = $instDataRouteConf->getRouteConfSubKeys('redis','mygroup');
var_dump($routeConfKeys);
$routeConf = $instDataRouteConf->getRouteConfByScript('redis','mygroup',array('id'=>3));
var_dump($routeConf);

