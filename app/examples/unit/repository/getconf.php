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

use Nickfan\AppBox\Config\BoxRepository;
use Nickfan\AppBox\Common\Usercache\ApcBoxUsercache;

$instRepository = new BoxRepository(new ApcBoxUsercache(),$app['path.storage'].'/conf');

$timezone = $instRepository->get('app.timezone');
var_dump($timezone);

$itemPerPages = $instRepository->get('common.itemPerPages');
var_dump($itemPerPages);
