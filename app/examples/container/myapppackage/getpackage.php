<?php
/**
 * Description
 *
 * @project appbox
 * @package 
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-07-02 16:58
 *
 */
require_once __DIR__ . '/../../../bootstrap/bootstrap.php';

use Nickfan\AppBox\Support\Facades\DataRouteInstance;

$instDataRouteInstance = DataRouteInstance::getStaticInstance();

use Nickfan\BoxApp\Package\UserPackage;
use Nickfan\BoxApp\Package\DummyPackage;

$instUserPackage = UserPackage::getInstance($instDataRouteInstance);
$instDummyPackage = DummyPackage::getInstance($instDataRouteInstance);

var_dump($instUserPackage->getObjectName());
var_dump($instUserPackage->getDefaultNamespace());

var_dump($instDummyPackage->getObjectName());
var_dump($instDummyPackage->getDefaultNamespace());

$instUserPackage->setDefaultNamespace('Nickfan\\BoxApp\\DataObject');
var_dump($instUserPackage->getDefaultNamespace());

$userObject = $instUserPackage->getDataObjectTemplateByLabel('user',array('id'=>123,'screen_name'=>'abc'));

var_dump($userObject);
