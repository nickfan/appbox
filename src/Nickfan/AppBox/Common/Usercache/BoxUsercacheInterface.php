<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-17 17:02
 *
 */

namespace Nickfan\AppBox\Common\Usercache;


interface BoxUsercacheInterface {

    public function getCacheDriverKey();
    
    public function setOption($option = array());

    public function getOption();

    public function get($key, $option = array());

    public function set($key, $val, $option = array());

    public function del($key, $option = array());

    public function exits($key, $option = array());

    public function flush($option = array());
}
