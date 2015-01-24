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


class RedisBoxUsercache extends BoxBaseUsercache implements BoxUsercacheInterface {
    protected $cachedriverkey = 'redis';

    protected $defaultOption = array(
        'redisHost' => '127.0.0.1:6379',
        'redisPersistent' => 1,
        'redisConnectTimeout' => '',
        'redisOptsSerializer' => '',
        'encode' => 'msgpack',
        'keyformat' => 'md5',
        'formatprefix' => '',
    );

    protected $instance = null;

    public function __construct($option = array()) {
        $this->setOption($option);
        return $this;
    }

    protected function getInstance() {
        if (is_null($this->instance)) {
            $settings = $this->getOption();
            $curInst = new Redis();
            $curHostInfo = explode(':', $settings['redisHost']);
            $host = $curHostInfo[0];
            $port = isset($curHostInfo[1]) ? $curHostInfo[1] : null;
            if (intval($settings['redisPersistent']) != 0) {
                if (!empty($port)) {
                    if (!empty($settings['redisConnectTimeout'])) {
                        if (!$curInst->pconnect($host, $port, $settings['redisConnectTimeout'])) {
                            throw new \RuntimeException(_('Redis Usercache init Failed'), 500);
                        }
                    } else {
                        if (!$curInst->pconnect($host, $port)) {
                            throw new \RuntimeException(_('Redis Usercache init Failed'), 500);
                        }
                    }
                } else {
                    if (!$curInst->pconnect($host)) {
                        throw new \RuntimeException(_('Redis Usercache init Failed'), 500);
                    }
                }
            } else {
                if (!empty($port)) {
                    if (!empty($settings['redisConnectTimeout'])) {
                        if (!$curInst->connect($host, $port, $settings['redisConnectTimeout'])) {
                            throw new \RuntimeException(_('Redis Usercache init Failed'), 500);
                        }
                    } else {
                        if (!$curInst->connect($host, $port)) {
                            throw new \RuntimeException(_('Redis Usercache init Failed'), 500);
                        }
                    }
                } else {
                    if (!$curInst->connect($host)) {
                        throw new \RuntimeException(_('Redis Usercache init Failed'), 500);
                    }
                }
            }
            //$curInst->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_IGBINARY);
            $this->instance = $curInst;
        }
        return $this->instance;
    }

    public function get($key, $option = array()) {
        $option += array(
            'ttl' => $this->defaultOption['ttl'],
        );
        $querykey = $this->formatKey($key);

        $result = $this->getInstance()->get($querykey);
        if ($result !== false) {
            return $this->decodeVal($result);
        }
    }

    public function set($key, $val, $option = array()) {
        $option += array(
            'ttl' => $this->defaultOption['ttl'],
        );
        $querykey = $this->formatKey($key);
        $queryval = $this->encodeVal($val);
        if ($option['ttl'] > 0) {
            $result = $this->getInstance()->setex($querykey, $option['ttl'], $queryval);
        } elseif ($option['ttl'] == 0) {
            $result = $this->getInstance()->set($querykey, $queryval);
        } else {
            $result = $this->getInstance()->delete($querykey);
        }
        return $result;
    }

    public function del($key, $option = array()) {
        $option += array(
            'ttl' => $this->defaultOption['ttl'],
        );
        $querykey = md5($key);
        return $this->getInstance()->delete($querykey);
    }

    public function exits($key, $option = array()) {
        $option += array(
            'ttl' => $this->defaultOption['ttl'],
        );
        $querykey = $this->formatKey($key);
        return $this->getInstance()->exists($querykey);
    }

    public function flush($option = array()) {
        $option += array(
            'ttl' => $this->defaultOption['ttl'],
        );
        return $this->getInstance()->flushAll();
    }

}
