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


use Nickfan\AppBox\Common\Exception\DataRouteInstanceException;
use Nickfan\AppBox\Instance\BaseDataRouteInstanceDriver;
use Nickfan\AppBox\Instance\DataRouteInstanceDriverInterface;
use ezSQL_mssql;
use ezSQL_sqlsrv;
use ezSQL_mysql;
class CfgDataRouteInstanceDriver extends BaseDataRouteInstanceDriver implements DataRouteInstanceDriverInterface {

    /**
     * do driver instance init
     */
    public function setup() {
        $settings = $this->getSettings();
        if (empty($settings)) {
            throw new DataRouteInstanceException('init driver instance failed: empty settings');
        }
        if(isset($settings['dbDriver']) && $settings['dbDriver']=='mssql'){
            if(strtoupper(substr(PHP_OS,0,3))=='WIN'){ // sqldrv on windows
                $curInst = new ezSQL_sqlsrv($settings['dbUser'], $settings['dbPasswd'], $settings['dbSchema'], $settings['dbHost'],TRUE);
            }else{	// mssql(sybase) on linux
                $curInst = new ezSQL_mssql($settings['dbUser'], $settings['dbPasswd'], $settings['dbSchema'], $settings['dbHost'],TRUE);
            }
            $curInst->cache_timeout = $settings['dbCacheTimeout'];
            $curInst->cache_dir = $settings['dbDiskCachePath'];
            $curInst->use_disk_cache = $settings['dbCache']==1;
            $curInst->cache_queries = $settings['dbCache']==1;
            if($settings['dbShowError']==1){
                $curInst->show_errors();
            }else{
                $curInst->hide_errors();
            }
            //$curInst->set_charset('utf8');
            //$curInst->quick_connect($settings['dbUser'], $settings['dbPasswd'], $settings['dbSchema'], $settings['dbHost']);
        }else{
            $curInst = new ezSQL_mysql($settings['dbUser'], $settings['dbPasswd'], $settings['dbSchema'], $settings['dbHost'],$settings['dbCharset']);
            $curInst->cache_timeout = $settings['dbCacheTimeout'];
            $curInst->cache_dir = $settings['dbDiskCachePath'];
            $curInst->use_disk_cache = $settings['dbCache']==1;
            $curInst->cache_queries = $settings['dbCache']==1;
            if($settings['dbShowError']==1){
                $curInst->show_errors();
            }else{
                $curInst->hide_errors();
            }
            //$curInst->set_charset($settings['dbCharset']);


            //$curInst->quick_connect($settings['dbUser'], $settings['dbPasswd'], $settings['dbSchema'], $settings['dbHost']);
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