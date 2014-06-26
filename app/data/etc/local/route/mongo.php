<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-23 10:55
 *
 */

return function ($routeKey, $attributes = array()) {
    $routeId = ''; // 路由id
    $groupKey = 'init'; // 默认分组名称
    $separateId = ''; // 默认分组key后缀
    switch ($routeKey) {
        case 'mygroup':
            $routeId = is_array($attributes) && array_key_exists('id', $attributes) ? intval($attributes['id']) : null;
            !is_null($routeId) && $separateId = $routeId % 2;
            break;
        case 'my':
        default:
            $separateId = '';
            $groupKey = 'init';
            break;
    }
    if ($separateId !== '' && $groupKey == 'init') {
        $groupKey = 'g'; // 默认分组key前缀
    }
    return array(
        'routeKey' => $routeKey,
        'group' => $groupKey . $separateId,
    );
};