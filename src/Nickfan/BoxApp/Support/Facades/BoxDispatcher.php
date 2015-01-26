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


namespace Nickfan\BoxApp\Support\Facades;

use Nickfan\AppBox\Support\Facades\Facade;
class BoxDispatcher extends Facade {
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'boxdispatcher';
    }
}
