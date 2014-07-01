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


class DataRouteInstance extends Facade {
    const DRIVER_KEY_DEFAULT = 'cfg';
    const DATAROUTE_MODE_ATTR = 0;      // dataroute mode by attributes
    const DATAROUTE_MODE_IDSET = 1;      // dataroute mode by routeIdSet
    const DATAROUTE_MODE_DIRECT = 3;     // dataroute mode by directsettings

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'datarouteinstance';
    }
} 