<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-25 14:15
 *
 */


namespace Nickfan\AppBox\Common\Usercache;

abstract class BaseUsercache implements UsercacheInterface {

    protected $defaultOption = array(
        'encode' => 'serialize',
        'keyformat' => 'md5',
        'formatprefix' => '',
        'ttl' => 300,
    );

    protected $instance = null;

    public function __construct($option = array()) {
        $this->setOption($option);
        return $this;
    }

    protected function getInstance() {
        if (is_null($this->instance)) {
            $this->instance = new \stdClass();
        }
        return $this->instance;
    }

    protected function setInstance($instance) {
        $this->instance = $instance;
    }

    public function setOption($option = array()) {
        if (!empty($option)) {
            $this->defaultOption = array_merge($this->defaultOption, $option);
        }
    }

    public function getOption() {
        return $this->defaultOption;
    }

    protected function encodeVal($val) {
        switch ($this->defaultOption['encode']) {
            case 'json':
                return json_encode($val);
                break;
            case 'msgpack':
                return msgpack_pack($val);
                break;
            case 'serialize':
            default:
                return serialize($val);
                break;
        }
    }

    protected function decodeVal($str) {
        switch ($this->defaultOption['encode']) {
            case 'json':
                return json_decode($str, true);
                break;
            case 'msgpack':
                return msgpack_unpack($str);
                break;
            case 'serialize':
            default:
                return unserialize($str);
                break;
        }
    }

    protected function formatKey($key) {
        switch ($this->defaultOption['keyformat']) {
            case 'md5':
                return $this->defaultOption['formatprefix'] . md5($key);
                break;
            case 'crc32':
                return $this->defaultOption['formatprefix'] . crc32($key);
                break;
            case 'none':
            default:
                return $this->defaultOption['formatprefix'] . $key;
                break;
        }
    }

    abstract function get($key, $option = array());

    abstract function set($key, $val, $option = array());

    abstract function del($key, $option = array());

    abstract function exits($key, $option = array());

    abstract function flush($option = array());

} 