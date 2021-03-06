<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-26 11:03
 *
 */


namespace Nickfan\AppBox\Instance\Drivers;


use Nickfan\AppBox\Common\Exception\BoxRouteInstanceException;
use Nickfan\AppBox\Instance\BoxBaseRouteInstanceDriver;
use Nickfan\AppBox\Instance\BoxRouteInstanceDriverInterface;
use ezSQL_mssql;
use ezSQL_sqlsrv;
use ezSQL_mysql;
class DbBoxRouteInstanceDriver extends BoxBaseRouteInstanceDriver implements BoxRouteInstanceDriverInterface {

    /**
     * do driver instance init
     */
    public function setup() {
        $settings = $this->getSettings();
        if (empty($settings)) {
            throw new BoxRouteInstanceException('init driver instance failed: empty settings');
        }
        $curInst = null;
        !isset($settings['dbDriver']) && $settings['dbDriver'] = 'mysql';
        switch($settings['dbDriver']){
            case 'mssql':
                if(strtoupper(substr(PHP_OS,0,3))=='WIN'){ // sqldrv on windows
                    $curInst = new ezSQL_sqlsrv($settings['dbUser'], $settings['dbPasswd'], $settings['dbSchema'], $settings['dbHost'],TRUE);
                }else{	// mssql(sybase) on linux
                    $curInst = new ezSQL_mssql($settings['dbUser'], $settings['dbPasswd'], $settings['dbSchema'], $settings['dbHost'],TRUE);
                }
                break;
            case 'mysql':
                $curInst = new ezSQL_mysql($settings['dbUser'], $settings['dbPasswd'], $settings['dbSchema'], $settings['dbHost'],$settings['dbCharset']);
                default:
                break;
        }
        //$curInst->quick_connect($settings['dbUser'], $settings['dbPasswd'], $settings['dbSchema'], $settings['dbHost']);
        $curInst->cache_timeout = $settings['dbCacheTimeout'];
        $curInst->cache_dir = $settings['dbDiskCachePath'];
        $curInst->use_disk_cache = $settings['dbCache']==1;
        $curInst->cache_queries = $settings['dbCache']==1;
        if($settings['dbShowError']==1){
            $curInst->show_errors();
        }else{
            $curInst->hide_errors();
        }
        $this->instance = $curInst;
        //$this->isAvailable = $this->instance ? true : false;
        $this->isAvailable = true;
    }

    public function close() {
        try {
            if($this->instance){
                $this->instance->disconnect();
            }
        } catch (\Exception $ex) {
        }
        $this->isAvailable = false;
    }
}