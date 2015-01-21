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

use Nickfan\AppBox\Common\Usercache\ApcBoxUsercache;
use Nickfan\AppBox\Config\BoxRouteConf;
use Nickfan\AppBox\Instance\BoxRouteInstance;

use Nickfan\BoxApp\BoxPackage\UserBoxPackage;
use Nickfan\BoxApp\BoxPackage\DummyBoxPackage;

$instBoxRouteInstance = BoxRouteInstance::getInstance(new BoxRouteConf($app['path']['storage'] . '/etc/local',new ApcBoxUsercache()));
//var_dump($instBoxRouteInstance);
//exit;
$instUserPackage = UserBoxPackage::getInstance($instBoxRouteInstance);
$instDummyPackage = DummyBoxPackage::getInstance($instBoxRouteInstance);

var_dump($instUserPackage->getObjectName());
var_dump($instUserPackage->getDefaultNamespace());

var_dump($instDummyPackage->getObjectName());
var_dump($instDummyPackage->getDefaultNamespace());

$instUserPackage->setDefaultNamespace('Nickfan\\BoxApp\\BoxObject');
var_dump($instUserPackage->getDefaultNamespace());

$userObject = $instUserPackage->getBoxObjectTemplateByLabel('user',array('id'=>123,'screen_name'=>'abc'));

var_dump($userObject);
