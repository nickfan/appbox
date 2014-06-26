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


use Nickfan\AppBox\Common\Exception\DataRouteInstanceException;

abstract class BaseDataRouteInstanceDriver implements DataRouteInstanceDriverInterface {
    public $instance = null;
    public $isAvailable = false;
    protected $routeIdSet = array();
    protected $settings = array();

    public function __construct($routeIdSet = array(), $settings = array()) {
        $this->routeIdSet = $routeIdSet;
        $this->settings = $settings;
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

    public function getRouteIdSet() {
        return $this->routeIdSet;
    }

    public function getSettings() {
        return $this->settings;
    }

    /**
     * get driver instance
     */
    public function getInstance() {
        if (!$this->instance) {
            $this->setup();
        }
        if ($this->isAvailable != true) {
            throw new DataRouteInstanceException(_('Instance init failed'), 500);
        }
        return $this->instance;
    }

    public function close() {
        return;
    }
} 