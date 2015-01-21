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


use Nickfan\AppBox\Common\BoxConstants;
use Nickfan\AppBox\Instance\BoxRouteInstanceInterface;

interface BoxRouteServiceDriverInterface {

    //public function __construct(BoxRouteInstance $instBoxRouteInstance);

    public function getBoxRouteInstance();

    public function setBoxRouteInstance(BoxRouteInstanceInterface $instBoxRouteInstance);

    public function getDriverKey();

    public function setDriverKey($driverKey = BoxConstants::DRIVER_KEY_DEFAULT);

    public function getRouteInstance($routeKey = BoxConstants::CONF_KEY_ROOT, $attributes = array(), $driverKey = null);

    public function getRouteInstanceRouteIdSet(
        $routeKey = BoxConstants::CONF_KEY_ROOT,
        $attributes = array(),
        $driverKey = null
    );

    public function getRouteConfKeysByRouteKey($routeKey = BoxConstants::CONF_KEY_ROOT, $driverKey = null);

    public function getRouteInstanceByConfSubset(
        $routeKey = BoxConstants::CONF_KEY_ROOT,
        $subset = BoxConstants::CONF_LABEL_INIT,
        $driverKey = null
    );


}