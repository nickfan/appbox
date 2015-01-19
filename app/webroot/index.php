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

require_once '../bootstrap/initenv.php';


$inst = \Nickfan\BoxApp\Dispatcher\MyDispatcher::getInstance($app);
$inst->run();
