<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-25 17:21
 *
 */


namespace Nickfan\AppBox\Instance;


interface DataRouteInstanceDriverInterface {

    public function __construct($routeIdSet = array(), $settings = array());

    public function setup();

    public function isAvailable();

    public function getInstance();

    public function close();

    public function getRouteIdSet();

    public function getSettings();
}