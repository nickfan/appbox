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



namespace Nickfan\BoxApp\BoxObject;


use Nickfan\AppBox\BoxObject\BoxObject;

class UserBoxObject extends BoxObject {
    const DO_VERSION = '20140702';            // 版本号

    const USER_GENDER_UNKNOWN = 0; //用户性别 未知/保密
    const USER_GENDER_MALE = 1; //用户性别 男
    const USER_GENDER_FEMALE = 2; //用户性别 女

    const USER_STATUS_INACTIVE = 0;     // 未激活/未启用
    const USER_STATUS_ACTIVE = 1;       // 已激活/已启用
    const USER_STATUS_IMPORTED = 2;     // 已导入（需要后续处理）
    const USER_STATUS_MERGED = 3;       // 已合并
    const USER_STATUS_DISABLED = 4;     // 已禁用
    const USER_STATUS_DELETED = 5;      // 已删除


    protected $props = array(
        'id'=>array(                            // 用户编号id
            'key'=>self::KEYTYPE_ID,            // keytype
            'type'=>self::TYPE_INT,             // prop var type
            'default'=>0,                       // default value
            'length'=>null,                     // data length limit
            'enabled'=>true,                    // enable this field
        ),
        'idstr'=>array(
            'key'=>self::KEYTYPE_IDSTR,         // keytype
            'type'=>self::TYPE_STRING,          // prop var type
            'default'=>'',                      // default value
            'length'=>null,                     // data length limit
            'enabled'=>false,                   // enable this field
        ),
//        '_id'=>array(
//            'key'=>self::KEYTYPE_UUID,          // keytype
//            'type'=>self::TYPE_STRING,          // prop var type
//            'default'=>'',                      // default value
//            'length'=>null,                     // data length limit
//            'enabled'=>false,                   // enable this field
//        ),
        '_version'=>array(
            'key'=>self::KEYTYPE_NONE,          // keytype
            'type'=>self::TYPE_STRING,          // prop var type
            'default'=>self::DO_VERSION,              // default value
            'length'=>null,                     // data length limit
            'enabled'=>false,                   // enable this field
        ),
        'status'=>array(                        // 用户状态
            'type'=>self::TYPE_INT,             // prop var type
            'default'=>self::USER_STATUS_INACTIVE,    // default value
        ),
        'screen_name'=>array(                     // 用户屏幕名(站内唯一)
            'type'=>self::TYPE_STRING,          // prop var type
            'default'=>'',                      // default value
        ),
        'gender'=>array(                      // 用户性别
            'type'=>self::TYPE_INT,          // prop var type
            'default'=>self::USER_GENDER_UNKNOWN,                      // default value
        ),

        'avatar_image_url'=>array(              // 用户头像地址
            'type'=>self::TYPE_STRING,          // prop var type
            'default'=>'',                      // default value
        ),

        'birthday'=>array(                      // 用户生日数字 2013-03-29
            'type'=>self::TYPE_STRING,          // prop var type
            'default'=>'',                      // default value
        ),

        'real_name'=>array(                      // 用户真实姓名
            'type'=>self::TYPE_STRING,          // prop var type
            'default'=>'',                      // default value
        ),

        'url'=>array(                           // 用户个人网址（博客）
            'type'=>self::TYPE_STRING,          // prop var type
            'default'=>'',                      // default value
        ),

        'description'=>array(                   // 用户个人简介
            'type'=>self::TYPE_STRING,          // prop var type
            'default'=>'',                      // default value
        ),

        'geo'=>array(                           // geo信息 经纬度 lat,lng
            'type'=>self::TYPE_STRING,          // prop var type
            'default'=>'',                      // default value
        ),

        'upts_screen_name'=>array(                // 最后更新时间戳 屏幕名称
            'type'=>self::TYPE_INT,             // prop var type
            'default'=>0,                       // default value
        ),
        'upts'=>array(                // 更新时间戳
            'type'=>self::TYPE_INT,             // prop var type
            'default'=>0,                       // default value
        ),
        'crts'=>array(                // 创建时间戳
            'type'=>self::TYPE_INT,             // prop var type
            'default'=>0,                       // default value
        ),
    );

} 