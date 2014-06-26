<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-10 20:18
 *
 */

$app = require_once '../bootstrap/bootstrap.php';


$inst = \Nickfan\MyApp\Dispatcher\MyDispatcher::getInstance($app);
$inst->run();
