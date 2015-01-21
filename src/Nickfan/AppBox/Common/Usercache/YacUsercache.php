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


class YacBoxBaseUsercache extends BoxBaseUsercache implements BoxUsercacheInterface {

    protected function getInstance() {
        if (is_null($this->instance)) {
            $this->instance = new Yac();
        }
        return $this->instance;
    }

    public function get($key, $option = array()) {
        $option += array(
            'ttl' => $this->defaultOption['ttl'],
        );
        $querykey = $this->formatKey($key);

        $getVal = $this->getInstance()->get($querykey);
        if (!is_null($getVal)) {
            return $this->decodeVal($getVal);
        } else {
            return null;
        }
    }

    public function set($key, $val, $option = array()) {
        $option += array(
            'ttl' => $this->defaultOption['ttl'],
        );
        $querykey = $this->formatKey($key);
        return $this->getInstance()->set($querykey, $this->encodeVal($val), $option['ttl']);
    }

    public function del($key, $option = array()) {
        $option += array(
            'ttl' => $this->defaultOption['ttl'],
        );
        $querykey = $this->formatKey($key);
        return $this->getInstance()->delete($querykey);
    }

    public function exits($key, $option = array()) {
        $option += array(
            'ttl' => $this->defaultOption['ttl'],
        );
        $querykey = $this->formatKey($key);
        $getVal = $this->getInstance()->get($querykey);
        return !is_null($getVal);
    }

    public function flush($option = array()) {
        $option += array(
            'ttl' => $this->defaultOption['ttl'],
        );
        return $this->getInstance()->flush();
    }

}