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

use Nickfan\AppBox\Common\Usercache\AutoBoxUsercache;

$instUserCache= new AutoBoxUsercache();

$instUserCache->flush();
$instUserCache->set('abc','1adf23');
$data = $instUserCache->get('abc');
echo $data.PHP_EOL;
echo $instUserCache->getCacheDriverKey().PHP_EOL;
$instUserCache->flush();

