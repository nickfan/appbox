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



namespace App\Boxpackages;


use Nickfan\AppBox\Package\BoxBasePackage;
use Nickfan\AppBox\Instance\BoxRouteInstanceInterface;

class UserBoxPackage extends BoxBasePackage{
    protected function __construct(BoxRouteInstanceInterface $instBoxRouteInstance = NULL, $objectName = ""){
        parent::__construct($instBoxRouteInstance,$objectName);
        //$this->setDefaultNamespace(__NAMESPACE__);
        return $this;
    }
}