<?php
/**
 * Description
 *
 * @project appbox
 * @package 
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-07-02 16:18
 *
 */



namespace Nickfan\MyApp\DataObject;


use Nickfan\AppBox\DataObject\BaseDataObject;

class UserDataObject extends BaseDataObject {
    const USER_GENDER_UNKNOWN = 0; //用户性别 未知/保密
    const USER_GENDER_MALE = 1; //用户性别 男
    const USER_GENDER_FEMALE = 2; //用户性别 女

    const USER_STATUS_INACTIVE = 0;     // 未激活/未启用
    const USER_STATUS_ACTIVE = 1;       // 已激活/已启用
    const USER_STATUS_IMPORTED = 2;     // 已导入（需要后续处理）
    const USER_STATUS_MERGED = 3;       // 已合并
    const USER_STATUS_DISABLED = 4;     // 已禁用
    const USER_STATUS_DELETED = 5;      // 已删除

    protected static $dataObjectUuidPropKey = '';
    protected static $dataObjectIdPropKey = 'id';
    protected static $dataObjectIdStrPropKey = '';
    //protected static $dataObjectVersionPropKey = '_version';
    protected static $dataObjectVersionPropValue = 20140702;
    protected $data = array(
        'id'=>0,                                            // 用户编号id
        'status'=>self::USER_STATUS_INACTIVE,               // 用户状态
        'screen_name'=>'',                                  // 用户屏幕名(站内唯一)
        'gender'=>self::USER_GENDER_UNKNOWN,                // 用户性别
        'avatar_image_url'=>'',                             // 用户头像地址
        'birthday'=>'',                                     // 用户生日数字 2013-03-29
        'real_name'=>'',                                    // 用户真实姓名
        'url'=>'',                                          // 用户个人网址（博客）
        'description'=>'',                                  // 用户个人简介
        'geo_long' => 0.0,									// geo信息 经度
        'geo_lat' => 0.0,									// geo信息 维度
        'upts_screen_name'=>0,                              // 最后更新屏幕名称时间戳
        'upts'=>0,                                          // 更新时间戳
        'crts'=>0,                                          // 创建时间戳
    );
} 