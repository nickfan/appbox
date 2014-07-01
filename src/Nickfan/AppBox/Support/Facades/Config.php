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


class Config extends Facade {

    const USERCACHE_TTL_DEFAULT = 300;

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'config';
    }
} 