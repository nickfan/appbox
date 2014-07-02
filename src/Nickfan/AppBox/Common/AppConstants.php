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
     * 实例类型
     */

    const INSTANCE_TYPE_CACHE = 'cache';        // 缓存
    const INSTANCE_TYPE_SEQ = 'seq';            // 序列生成器
    const INSTANCE_TYPE_DB = 'db';              // 数据库
    const INSTANCE_TYPE_IDX = 'idx';            // 索引（全文检索）
    const INSTANCE_TYPE_MQ = 'mq';              // 消息队列
    const INSTANCE_TYPE_FS = 'fs';              // 文件存储
    const INSTANCE_TYPE_RPC = 'rpc';            // 远程调用
    const INSTANCE_TYPE_DC = 'dc';              // 分布式计算

    /**
     * 缓存实例类型
     */
    const INSTANCE_CACHE_DRIVER_NONE = '';
    const INSTANCE_CACHE_DRIVER_MEMCACHE = 'memcache';
    const INSTANCE_CACHE_DRIVER_REDIS = 'redis';

    /**
     * 序列实例类型
     */
    const INSTANCE_SEQ_DRIVER_NONE = '';
    const INSTANCE_SEQ_DRIVER_MONGODB = 'mongo';
    const INSTANCE_SEQ_DRIVER_REDIS = 'redis';
    const INSTANCE_SEQ_DRIVER_MYSQL = 'mysql';
    const INSTANCE_SEQ_DRIVER_MSSQL = 'mssql';

    /**
     * 数据库实例类型
     */
    const INSTANCE_DB_DRIVER_NONE = '';
    const INSTANCE_DB_DRIVER_MONGODB = 'mongo';
    const INSTANCE_DB_DRIVER_REDIS = 'redis';
    const INSTANCE_DB_DRIVER_MYSQL = 'mysql';
    const INSTANCE_DB_DRIVER_MSSQL = 'mssql';

    /**
     * 索引实例类型
     */
    const INSTANCE_IDX_DRIVER_NONE = '';
    const INSTANCE_IDX_DRIVER_SPHINX = 'sphinx';
    const INSTANCE_IDX_DRIVER_XAPIAN = 'xapian';
    const INSTANCE_IDX_DRIVER_LUCENE = 'lucene';
    const INSTANCE_IDX_DRIVER_CUSTOM = 'custom';

    /**
     * 消息/任务队列实例类型
     */
    const INSTANCE_MQ_DRIVER_NONE = '';
    const INSTANCE_MQ_DRIVER_BEANSTALK = 'beanstalk';
    const INSTANCE_MQ_DRIVER_GEARMAN = 'gearman';
    const INSTANCE_MQ_DRIVER_ZMQ = 'zmq';
    const INSTANCE_MQ_DRIVER_REDIS = 'redis';
    const INSTANCE_MQ_DRIVER_AMQP = 'amqp';

    /**
     * 文件系统实例类型
     */
    const INSTANCE_FS_DRIVER_NONE = '';
    const INSTANCE_FS_DRIVER_FUSE = 'fuse';
    const INSTANCE_FS_DRIVER_GRIDFS = 'gridfs';
    const INSTANCE_FS_DRIVER_FASTDFS = 'fastdfs';

    /**
     * 远程调用实例类型
     */
    const INSTANCE_RPC_DRIVER_NONE = '';
    const INSTANCE_RPC_DRIVER_THRIFT = 'thrift';
    const INSTANCE_RPC_DRIVER_ICE = 'ice';

    /**
     * 分布式计算实例类型
     */
    const INSTANCE_DC_DRIVER_NONE = '';
    const INSTANCE_DC_DRIVER_HADOOP = 'hadoop';
    const INSTANCE_DC_DRIVER_STORM = 'storm';
    const INSTANCE_DC_DRIVER_SPARK = 'spark';

} 