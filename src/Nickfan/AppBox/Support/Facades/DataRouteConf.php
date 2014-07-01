<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-18 11:02
 *
 */


namespace Nickfan\AppBox\Support\Facades;


class DataRouteConf extends Facade {
    const CONF_KEY_ROOT = 'root';
    const CONF_LABEL_INIT = 'init';
    const USERCACHE_TTL_DEFAULT = 300;

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'datarouteconf';
    }
} 