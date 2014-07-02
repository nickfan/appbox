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

    protected static $dataObjectUuidPropKey = '';
    protected static $dataObjectIdPropKey = 'id';
    protected static $dataObjectIdStrPropKey = '';
    //protected static $dataObjectVersionPropKey = '_version';
    protected static $dataObjectVersionPropValue = 20140702;
    protected $data = array(
        'id'=>0,                                                        // 用户编号id
        'accountid'=>'',                                                // 登陆名
        'mailaddr'=>'',                                                 // 登陆邮箱
        'mobilenum'=>'',                                                // 登陆手机号
        'authsecret'=>'',                                               // 验证密钥
        'passstatus'=>self::ACCOUNT_PASSSTATUS_SET,                     // 本地登陆密码状态
        'passhash'=>'',                                                 // 账户密码hash
        'passsalt'=>'',                                                 // 账户密码随机种子
        'secques'=>'',                                                  // 账户安全问题
        'verify_mail'=> self::ACCOUNT_VERIFYMAIL_STATUS_NO,             // 账户邮箱验证状态
        'verify_mobile'=> self::ACCOUNT_VERIFYMOBILE_STATUS_NO,         // 账户手机验证状态
        'verify_credential'=> self::ACCOUNT_VERIFYCREDENTIAL_STATUS_NO, // 账户证件验证状态
        'regfromtype'=> self::ACCOUNT_REGFROMTYPE_SITE,                 // 账户注册来源类型
        'regip'=> '',                                                   // 账户注册来源ip
        'credentialid'=> '',                                            // 账户证件ID
        'credentialtype'=> self::ACCOUNT_CREDENTIAL_TYPE_ID,            // 账户证件类型
        'openbind'=>'',                                                 // 第三方绑定列表
        'upts_accountid'=>0,                                            // 最后更新时间 登录名
        'upts_mailaddr'=>0,                                             // 最后更新时间 邮件地址
        'upts_mobilenum'=>0,                                            // 最后更新时间 手机号码
        'upts'=>0,                                                      // 更新时间戳
        'crts'=>0,                                                      // 创建时间戳
    );
} 