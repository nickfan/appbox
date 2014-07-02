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

use Nickfan\AppBox\Common\Usercache\ApcUsercache;
use Nickfan\AppBox\Config\DataRouteConf;
use Nickfan\AppBox\Instance\DataRouteInstance;

use Nickfan\MyApp\Package\UserPackage;
use Nickfan\MyApp\Package\DummyPackage;

$instDataRouteInstance = DataRouteInstance::getInstance(new DataRouteConf(new ApcUsercache(), $app['path.storage'] . '/etc/local'));
//var_dump($instDataRouteInstance);
//exit;
$instUserPackage = UserPackage::getInstance($instDataRouteInstance);
$instDummyPackage = DummyPackage::getInstance($instDataRouteInstance);

var_dump($instUserPackage->getObjectName());
var_dump($instUserPackage->getDefaultNamespace());

var_dump($instDummyPackage->getObjectName());
var_dump($instDummyPackage->getDefaultNamespace());

$instUserPackage->setDefaultNamespace('Nickfan\\MyApp\\DataObject');
var_dump($instUserPackage->getDefaultNamespace());

$userObject = $instUserPackage->getDataObjectTemplateByLabel('user',array('id'=>123,'screen_name'=>'abc'));

var_dump($userObject);
