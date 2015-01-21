<?php
/**
 * Description
 *
 * @project appbox
 * @package 
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-07-02 17:14
 *
 */

require_once __DIR__ . '/../../../bootstrap/initenv.php';

use Nickfan\AppBox\Common\Usercache\ApcBoxBaseUsercache;
use Nickfan\AppBox\Config\BoxRouteConf;
use Nickfan\AppBox\Instance\BoxRouteInstance;

use Nickfan\BoxApp\Package\UserBasePackage;
use Nickfan\BoxApp\Package\DummyBasePackage;

$instDataRouteInstance = BoxRouteInstance::getInstance(new BoxRouteConf(new ApcBoxBaseUsercache(), $app['path.storage'] . '/etc/local'));
//var_dump($instDataRouteInstance);
//exit;
$instUserPackage = UserBasePackage::getInstance($instDataRouteInstance);
$instDummyPackage = DummyBasePackage::getInstance($instDataRouteInstance);

var_dump($instUserPackage->getObjectName());
var_dump($instUserPackage->getDefaultNamespace());

var_dump($instDummyPackage->getObjectName());
var_dump($instDummyPackage->getDefaultNamespace());

$instUserPackage->setDefaultNamespace('Nickfan\\BoxApp\\DataObject');
var_dump($instUserPackage->getDefaultNamespace());

$userObject = $instUserPackage->getDataObjectTemplateByLabel('user',array('id'=>123,'screen_name'=>'abc'));

var_dump($userObject);
