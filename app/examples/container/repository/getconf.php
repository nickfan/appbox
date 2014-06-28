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

use Nickfan\AppBox\Support\Facades\Config;



$timezone = Config::get('app.timezone');
var_dump($timezone);

$itemPerPages = Config::get('common.itemPerPages');
var_dump($itemPerPages);
