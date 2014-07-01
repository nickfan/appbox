<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-30 13:52
 *
 */


namespace Nickfan\AppBox\Service\Drivers;


use Nickfan\AppBox\Service\BaseDataRouteServiceDriver;
use Nickfan\AppBox\Service\DataRouteServiceDriverInterface;

class RedisDataRouteServiceDriver extends BaseDataRouteServiceDriver implements DataRouteServiceDriverInterface {

    //protected static $driverKey = 'redis';

    public function get($key, $option = array(), $driverInstance = null) {
        $option += array();
        list($driverInstance, $option) = $this->getVendorSerivceInstanceSet(
            $option,
            $driverInstance,
            array('key' => $key,)
        );
        return $driverInstance->get($key);
    }

    public function set($key, $val = '', $option = array(), $driverInstance = null) {
        $option += array();
        list($driverInstance, $option) = $this->getVendorSerivceInstanceSet(
            $option,
            $driverInstance,
            array('key' => $key,)
        );
        return $driverInstance->set($key, $val);
    }

} 