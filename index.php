#!/usr/bin/env php
<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-07-03 14:14
 *
 */


if (php_sapi_name() != 'cli') {
    require_once __DIR__ . '/public/index.php';
} else {
    require_once __DIR__ . '/public/index.php';
    //require_once __DIR__ . '/appbox';
}
