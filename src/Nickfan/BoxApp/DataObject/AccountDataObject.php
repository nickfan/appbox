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



namespace Nickfan\BoxApp\DataObject;


use Nickfan\AppBox\DataObject\BaseDataObject;

class AccountDataObject extends BaseDataObject {
    const ACCOUNT_PASSSTATUS_NOTSET = 0;     //  本地密码状态,未激活/未启用
    const ACCOUNT_PASSSTATUS_SET = 1;        //  本地密码状态,已激活/已启用
    const ACCOUNT_PASSSTATUS_IMPORTED = 2;   //  本地密码状态,被导入的（需要做后续的更新）

    const ACCOUNT_VERIFYMAIL_STATUS_NO = 0; // 账户邮箱验证状态 未验证
    const ACCOUNT_VERIFYMAIL_STATUS_YES = 1; // 账户邮箱验证状态 验证通过
    const ACCOUNT_VERIFYMOBILE_STATUS_NO = 0; // 账户手机验证状态 未验证
    const ACCOUNT_VERIFYMOBILE_STATUS_YES = 1; // 账户手机验证状态 验证通过
    const ACCOUNT_CREDENTIAL_TYPE_ID = 0;			// 证件类型 身份证
    const ACCOUNT_CREDENTIAL_TYPE_PASSPORT = 1;			// 证件类型 护照
    const ACCOUNT_VERIFYCREDENTIAL_STATUS_NO = 0; // 账户证件验证状态 未验证
    const ACCOUNT_VERIFYCREDENTIAL_STATUS_YES = 1; // 账户证件验证状态 验证通过

    const ACCOUNT_REGFROMTYPE_SITE = 0 ;  // 注册来源 本地站点
    const ACCOUNT_REGFROMTYPE_OPEN = 1 ;  // 注册来源 开放平台
    const ACCOUNT_REGFROMTYPE_API = 2 ;	  // 注册来源 第三方API

    const ACCOUNT_IDLABEL_USERNAME = 0; // 账户唯一标识 屏幕名号
    const ACCOUNT_IDLABEL_MAIL = 1; // 账户唯一标识 邮箱地址
    const ACCOUNT_IDLABEL_MOBILE = 2; // 账户唯一标识 手机地址
    const ACCOUNT_IDLABEL_ACCOUNTID = 3; // 账户唯一标识 登录ID名

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
            'default'=>'20140702',              // default value
            'length'=>null,                     // data length limit
            'enabled'=>false,                   // enable this field
        ),
        'accountid'=>array(                     // 登陆名
            'type'=>self::TYPE_STRING,          // prop var type
            'default'=>'',                      // default value
        ),
        'mailaddr'=>array(                      // 登陆邮箱
            'type'=>self::TYPE_STRING,          // prop var type
            'default'=>'',                      // default value
        ),
        'mobilenum'=>array(                     // 登陆手机号
            'type'=>self::TYPE_STRING,          // prop var type
            'default'=>'',                      // default value
        ),
        'authsecret'=>array(                    // 验证密钥
            'type'=>self::TYPE_STRING,          // prop var type
            'default'=>'',                      // default value
        ),
        'passstatus'=>array(                    // 本地登陆密码状态
            'type'=>self::TYPE_INT,             // prop var type
            'default'=>self::ACCOUNT_PASSSTATUS_SET,    // default value
        ),
        'passhash'=>array(                      // 账户密码hash
            'type'=>self::TYPE_STRING,          // prop var type
            'default'=>'',                      // default value
        ),
        'passsalt'=>array(                      // 账户密码随机种子
            'type'=>self::TYPE_STRING,          // prop var type
            'default'=>'',                      // default value
        ),
        'secques'=>array(                      // 账户安全问题
            'type'=>self::TYPE_STRING,          // prop var type
            'default'=>'',                      // default value
        ),
        'verify_mail'=>array(                    // 账户邮箱验证状态
            'type'=>self::TYPE_INT,             // prop var type
            'default'=>self::ACCOUNT_VERIFYMAIL_STATUS_NO,    // default value
        ),
        'verify_mobile'=>array(                    // 账户手机验证状态
            'type'=>self::TYPE_INT,             // prop var type
            'default'=>self::ACCOUNT_VERIFYMOBILE_STATUS_NO,    // default value
        ),
        'verify_credential'=>array(                    // 账户证件验证状态
            'type'=>self::TYPE_INT,             // prop var type
            'default'=>self::ACCOUNT_VERIFYCREDENTIAL_STATUS_NO,    // default value
        ),
        'regfromtype'=>array(                    // 账户注册来源类型
            'type'=>self::TYPE_INT,             // prop var type
            'default'=>self::ACCOUNT_REGFROMTYPE_SITE,    // default value
        ),
        'regip'=>array(                      // 账户注册来源ip
            'type'=>self::TYPE_STRING,          // prop var type
            'default'=>'',                      // default value
        ),
        'credentialid'=>array(                  // 账户证件ID
            'type'=>self::TYPE_STRING,          // prop var type
            'default'=>'',                      // default value
        ),
        'credentialtype'=>array(                    // 账户证件类型
            'type'=>self::TYPE_INT,             // prop var type
            'default'=>self::ACCOUNT_CREDENTIAL_TYPE_ID,    // default value
        ),
        'openbind'=>array(                      // 第三方绑定列表
            'type'=>self::TYPE_OBJECT,          // prop var type
            'default'=>'',                      // default value
        ),
        'upts_accountid'=>array(                // 最后更新时间 登录名
            'type'=>self::TYPE_INT,             // prop var type
            'default'=>0,                       // default value
        ),
        'upts_mailaddr'=>array(                // 最后更新时间 邮件地址
            'type'=>self::TYPE_INT,             // prop var type
            'default'=>0,                       // default value
        ),
        'upts_mobilenum'=>array(                // 最后更新时间 手机号码
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