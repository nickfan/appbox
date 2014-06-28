<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-28 13:57
 *
 */

require_once __DIR__.'/autoload.php';

!defined('APPBOX_PATH_BASE') && define('APPBOX_PATH_BASE',realpath(__DIR__.'/../../'));
!defined('APPBOX_PATH_APP') && define('APPBOX_PATH_APP',APPBOX_PATH_BASE.'/app');
!defined('APPBOX_PATH_DATA') && define('APPBOX_PATH_DATA',APPBOX_PATH_APP.'/data');
!defined('APPBOX_PATH_WEB') && define('APPBOX_PATH_WEB',APPBOX_PATH_APP.'/webroot');

!isset($app) && $app = array();
$app['path.base']= APPBOX_PATH_BASE;
$app['path.app']= APPBOX_PATH_APP;
$app['path.storage']= APPBOX_PATH_DATA;
$app['path.public']= APPBOX_PATH_WEB;
