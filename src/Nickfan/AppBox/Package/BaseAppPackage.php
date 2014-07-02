<?php
/**
 * Description
 *
 * @project appbox
 * @package 
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-07-02 11:00
 *
 */



namespace Nickfan\AppBox\Package;
use Nickfan\AppBox\Common\AppConstants;

abstract class BaseAppPackage {


    public static $cacheInstanceType = AppConstants::CACHEINSTANCE_TYPE_REDIS;
    public static $seqInstanceType = AppConstants::SEQINSTANCE_TYPE_MONGODB;
    public static $dbInstanceType = AppConstants::SEQINSTANCE_TYPE_MONGODB;
    public static $indexInstanceType = AppConstants::IDXINSTANCE_TYPE_SPHINX;
    public static $mqInstanceType = AppConstants::MQINSTANCE_TYPE_BEANSTALK;

    protected $objectName = '';    // ClassBase
} 