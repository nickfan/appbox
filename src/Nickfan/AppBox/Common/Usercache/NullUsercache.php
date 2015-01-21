<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-17 17:19
 *
 */


namespace Nickfan\AppBox\Common\Usercache;


class NullBoxBaseUsercache extends BoxBaseUsercache implements BoxUsercacheInterface {

    protected function getInstance() {
        if (is_null($this->instance)) {
            $this->instance = new \stdClass();
        }
        return $this->instance;
    }

    public function get($key, $option = array()) {
        $option += array(
            'ttl' => $this->defaultOption['ttl'],
        );
        $querykey = $this->formatKey($key);
        return isset($this->getInstance()->$querykey) ? $this->decodeVal($this->getInstance()->$querykey) : null;
    }

    public function set($key, $val, $option = array()) {
        $option += array(
            'ttl' => $this->defaultOption['ttl'],
        );
        $querykey = $this->formatKey($key);
        $this->getInstance()->$querykey = $this->encodeVal($val);
    }

    public function del($key, $option = array()) {
        $option += array(
            'ttl' => $this->defaultOption['ttl'],
        );
        $querykey = $this->formatKey($key);
        unset($this->getInstance()->$querykey);
    }

    public function exits($key, $option = array()) {
        $option += array(
            'ttl' => $this->defaultOption['ttl'],
        );
        $querykey = $this->formatKey($key);
        return isset($this->getInstance()->$querykey);
    }

    public function flush($option = array()) {
        $option += array(
            'ttl' => $this->defaultOption['ttl'],
        );
        $this->setInstance(new \stdClass());
        return true;
    }

}