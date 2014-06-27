<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-26 11:03
 *
 */


namespace Nickfan\AppBox\Instance\Drivers;

use Nickfan\AppBox\Common\Exception\DataRouteInstanceException;
use Nickfan\AppBox\Instance\BaseDataRouteInstanceDriver;
use Nickfan\AppBox\Instance\DataRouteInstanceDriverInterface;

class RedisDataRouteInstanceDriver extends BaseDataRouteInstanceDriver implements DataRouteInstanceDriverInterface {

    /**
     * do driver instance init
     */
    public function setup() {
        $settings = $this->getSettings();
        if (empty($settings)) {
            throw new DataRouteInstanceException('init driver instance failed: empty settings');
        }

        $curInst = new Redis();

        $curHostInfo = explode(':', $settings['redisHost']);
        $host = $curHostInfo[0];
        $port = isset($curHostInfo[1]) ? $curHostInfo[1] : null;
        if (intval($settings['redisPersistent']) != 0) {
            if (!empty($port)) {
                if (!empty($settings['redisConnectTimeout'])) {
                    if (!$curInst->pconnect($host, $port, $settings['redisConnectTimeout'])) {
                        throw new DataRouteInstanceException('Instance init failed:could not connect');
                    }
                } else {
                    if (!$curInst->pconnect($host, $port)) {
                        throw new DataRouteInstanceException('Instance init failed:could not connect');
                    }
                }
            } else {
                if (!$curInst->pconnect($host)) {
                    throw new DataRouteInstanceException('Instance init failed:could not connect');
                }
            }
        } else {
            if (!empty($port)) {
                if (!empty($settings['redisConnectTimeout'])) {
                    if (!$curInst->connect($host, $port, $settings['redisConnectTimeout'])) {
                        throw new DataRouteInstanceException('Instance init failed:could not connect');
                    }
                } else {
                    if (!$curInst->connect($host, $port)) {
                        throw new DataRouteInstanceException('Instance init failed:could not connect');
                    }
                }
            } else {
                if (!$curInst->connect($host)) {
                    throw new DataRouteInstanceException('Instance init failed:could not connect');
                }
            }
        }
        //$curInst->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_IGBINARY);
        $this->instance = $curInst;
        $this->isAvailable = $this->instance ? true : false;
    }

    public function close() {
        try {
            if($this->instance){
                $this->instance->close();
            }
        } catch (\Exception $ex) {
        }
        $this->isAvailable = false;
    }
}