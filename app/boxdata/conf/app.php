<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-16 14:41
 *
 */

return array(

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => true,


    'namespace'=>array(
        'cli'=>'',
        //'cli'=>'\\App\\Boxcommands',
        //'cli'=>'\\Nickfan\\BoxApp\\BoxCommand',
        'web'=>'',
        //'web'=>'\\App\\Boxcontrollers',
        //'web'=>'\\Nickfan\\BoxApp\\BoxController',

        'object'=>'',
        //'object'=>'\\App\\Boxobjects',
        //'object'=>'\\Nickfan\\AppBox\\BoxObject',
    ),
    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'Asia/Shanghai',
    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'zh-CN',
    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => array(
        'ClassLoader' => 'Nickfan\AppBox\Support\ClassLoader',
        'AppBox' => 'Nickfan\AppBox\Support\Facades\AppBox',
        'BoxDict' => 'Nickfan\AppBox\Support\Facades\BoxDict',
        'BoxConf' => 'Nickfan\AppBox\Support\Facades\BoxConf',
        'BoxRouteConf' => 'Nickfan\AppBox\Support\Facades\BoxRouteConf',
        'BoxRouteInst' => 'Nickfan\AppBox\Support\Facades\BoxRouteInst',
        'BoxDispatcher' => 'Nickfan\BoxApp\Support\Facades\BoxDispatcher',
        'BoxView' => 'Nickfan\BoxApp\Support\Facades\BoxView',
    ),

);
