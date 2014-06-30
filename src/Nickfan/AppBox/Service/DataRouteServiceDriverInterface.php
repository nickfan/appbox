<?php
/**
 * Description
 *
 * @project appbox
 * @package 
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-30 13:04
 *
 */



namespace Nickfan\AppBox\Service;


use Nickfan\AppBox\Instance\DataRouteInstance;
use Nickfan\AppBox\Config\DataRouteConf;

interface DataRouteServiceDriverInterface {

    //public function __construct(DataRouteInstance $instDataRouteInstance);

    public function getDataRouteInstance();

    public function setDataRouteInstance(DataRouteInstance $instDataRouteInstance);

    public function getDriverKey();

    public function setDriverKey($driverKey=DataRouteInstance::DRIVER_KEY_DEFAULT);

    public function getRouteInstance($routeKey = DataRouteConf::CONF_KEY_ROOT, $attributes = array(),$driverKey = null);

    public function getRouteInstanceRouteIdSet($routeKey = DataRouteConf::CONF_KEY_ROOT, $attributes = array(),$driverKey = null);

    public function getRouteConfKeysByRouteKey($routeKey = DataRouteConf::CONF_KEY_ROOT,$driverKey = null);

    public function getRouteInstanceByConfSubset($routeKey = DataRouteConf::CONF_KEY_ROOT,$subset=DataRouteConf::CONF_LABEL_INIT,$driverKey = null);



}