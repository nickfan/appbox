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

use Nickfan\AppBox\Config\Repository;
use Nickfan\AppBox\Common\Usercache\ApcUsercache;

$instRepository = new Repository(new ApcUsercache(),$app['path.storage'].'/conf');

echo $instRepository->getVersion();