<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-25 17:28
 *
 */


namespace Nickfan\AppBox\Instance;


use Nickfan\AppBox\Common\Exception\BoxRouteInstanceException;

abstract class BoxBaseRouteInstanceDriver implements BoxRouteInstanceDriverInterface {
    public $instance = null;
    public $isAvailable = false;
    protected $routeIdSet = array();
    protected $settings = array();

    public function __construct($settings = array(), $routeIdSet = array()) {
        $this->settings = $settings;
        $this->routeIdSet = $routeIdSet;
        //$this->setup();
    }

    public function __destruct() {
        $this->close();
    }

    /**
     * do driver instance init
     */
    public abstract function setup();

    public function isAvailable() {
        if (!$this->instance) {
            $this->setup();
        }
        return $this->isAvailable;
    }

    public function getSettings() {
        return $this->settings;
    }

    public function getSettingByKey($key='') {
        return isset($this->settings[$key])?$this->settings[$key]:null;
    }

    public function getRouteIdSet() {
        return $this->routeIdSet;
    }

    /**
     * get driver instance
     */
    public function getInstance() {
        if (!$this->instance) {
            $this->setup();
        }
        if ($this->isAvailable != true) {
            throw new BoxRouteInstanceException(_('Instance init failed'), 500);
        }
        return $this->instance;
    }

    public function close() {
        return;
    }
} 