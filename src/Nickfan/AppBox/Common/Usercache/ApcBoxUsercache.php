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


class ApcBoxUsercache extends BoxBaseUsercache implements BoxUsercacheInterface {

    public function get($key, $option = array()) {
        $option += array(
            'ttl' => $this->defaultOption['ttl'],
        );
        $querykey = $this->formatKey($key);
        $ok = false;
        $result = apc_fetch($querykey, $ok);
        if ($ok == true) {
            return $this->decodeVal($result);
        } else {
            return null;
        }
    }

    public function set($key, $val, $option = array()) {
        $option += array(
            'ttl' => $this->defaultOption['ttl'],
        );
        $querykey = $this->formatKey($key);
        return apc_store($querykey, $this->encodeVal($val), $option['ttl']);
    }

    public function del($key, $option = array()) {
        $option += array(
            'ttl' => $this->defaultOption['ttl'],
        );
        $querykey = $this->formatKey($key);
        return apc_delete($querykey);
    }

    public function exits($key, $option = array()) {
        $option += array(
            'ttl' => $this->defaultOption['ttl'],
        );
        $querykey = $this->formatKey($key);
        return apc_exists($querykey);
    }

    public function flush($option = array()) {
        $option += array(
            'ttl' => $this->defaultOption['ttl'],
        );
        return apc_clear_cache('user');
    }

}