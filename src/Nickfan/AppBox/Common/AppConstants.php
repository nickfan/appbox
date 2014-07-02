<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-07-02 11:08
 *
 */


namespace Nickfan\AppBox\Common;


class AppConstants {

    const VERSION = '0.1';
    const CONF_KEY_ROOT = 'root';
    const CONF_LABEL_INIT = 'init';
    const USERCACHE_TTL_DEFAULT = 300;

    const DRIVER_KEY_DEFAULT = 'cfg';
    const DATAROUTE_MODE_ATTR = 0; // dataroute mode by attributes
    const DATAROUTE_MODE_IDSET = 1; // dataroute mode by routeIdSet
    const DATAROUTE_MODE_DIRECT = 3; // dataroute mode by directsettings

    /**
     * 缓存实例类型
     */
    const CACHEINSTANCE_TYPE_NONE = '';
    const CACHEINSTANCE_TYPE_MEMCACHE = 'memcache';
    const CACHEINSTANCE_TYPE_REDIS = 'redis';

    /**
     * 序列实例类型
     */
    const SEQINSTANCE_TYPE_NONE = '';
    const SEQINSTANCE_TYPE_MONGODB = 'mongo';
    const SEQINSTANCE_TYPE_REDIS = 'redis';
    const SEQINSTANCE_TYPE_MYSQL = 'mysql';
    const SEQINSTANCE_TYPE_MSSQL = 'mssql';

    /**
     * 数据库实例类型
     */
    const DBINSTANCE_TYPE_NONE = '';
    const DBINSTANCE_TYPE_MONGODB = 'mongo';
    const DBINSTANCE_TYPE_REDIS = 'redis';
    const DBINSTANCE_TYPE_MYSQL = 'mysql';
    const DBINSTANCE_TYPE_MSSQL = 'mssql';

    /**
     * 索引实例类型
     */
    const IDXINSTANCE_TYPE_NONE = '';
    const IDXINSTANCE_TYPE_SPHINX = 'sphinx';
    const IDXINSTANCE_TYPE_XAPIAN = 'xapian';
    const IDXINSTANCE_TYPE_LUCENE = 'lucene';
    const IDXINSTANCE_TYPE_CUSTOM = 'custom';

    /**
     * 消息/任务队列实例类型
     */
    const MQINSTANCE_TYPE_NONE = '';
    const MQINSTANCE_TYPE_BEANSTALK = 'beanstalk';
    const MQINSTANCE_TYPE_GEARMAN = 'gearman';
    const MQINSTANCE_TYPE_ZMQ = 'zmq';
    const MQINSTANCE_TYPE_REDIS = 'redis';
    const MQINSTANCE_TYPE_AMQP = 'amqp';

    /**
     * 文件系统实例类型
     */
    const FSINSTANCE_TYPE_NONE = '';
    const FSINSTANCE_TYPE_FUSE = 'fuse';
    const FSINSTANCE_TYPE_GRIDFS = 'gridfs';
    const FSINSTANCE_TYPE_FASTDFS = 'fastdfs';

    /**
     * 远程调用实例类型
     */
    const RPCINSTANCE_TYPE_NONE = '';
    const RPCINSTANCE_TYPE_THRIFT = 'thrift';
    const RPCINSTANCE_TYPE_ICE = 'ice';

    /**
     * 分布式计算实例类型
     */
    const DCINSTANCE_TYPE_NONE = '';
    const DCINSTANCE_TYPE_HADOOP = 'hadoop';
    const DCINSTANCE_TYPE_STORM = 'storm';
    const DCINSTANCE_TYPE_SPARK = 'spark';

} 