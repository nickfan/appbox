<?php
/**
 * Description
 *
 * @project appbox
 * @package 
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-07-02 16:53
 *
 */



namespace Nickfan\MyApp\Package;


use Nickfan\AppBox\Package\BaseAppPackage;
use Nickfan\AppBox\Instance\DataRouteInstanceInterface;

class UserPackage extends BaseAppPackage{
    protected function __construct(DataRouteInstanceInterface $instDataRouteInstance = NULL, $objectName = ""){
        parent::__construct($instDataRouteInstance,$objectName);
        //$this->setDefaultNamespace(__NAMESPACE__);
        return $this;
    }
} 