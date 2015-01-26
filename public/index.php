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

require_once __DIR__ . '/../bootstrap/app_init.php';

$inst = \Nickfan\BoxApp\BoxDispatcher\DefaultBoxDispatcher::getInstance($app);
$inst->run();
