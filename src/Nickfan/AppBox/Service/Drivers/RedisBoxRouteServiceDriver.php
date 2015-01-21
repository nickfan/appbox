<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-30 13:52
 *
 */


namespace Nickfan\AppBox\Service\Drivers;


use Nickfan\AppBox\Service\BoxBaseRouteServiceDriver;
use Nickfan\AppBox\Service\BoxRouteServiceDriverInterface;

class RedisBoxRouteServiceDriver extends BoxBaseRouteServiceDriver implements BoxRouteServiceDriverInterface {

    //protected static $driverKey = 'redis';

    public function get($key, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $key,)
        );
        return $vendorInstance->get($key);
    }

    public function set($key, $val = '', $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $key,)
        );
        return $vendorInstance->set($key, $val);
    }


    /**
     * 生成序列id
     * @param string $objectName
     * @param array $option
     * @param null $vendorInstance
     * @return int
     */
    public function nextSeq($objectName = '',$option = array(), $vendorInstance = null){
        $option += array(
            'routeKey'=>'Seq',
            'init' => 0,
            'step' => 1,
        );
        empty($objectName) && $objectName = $this->getRouteKey();
        $seqModLabel = lcfirst($option['routeKey']);

        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $objectName,)
        );


        $respInfo = $vendorInstance->multi()
            ->setnx($seqModLabel.'_'.$objectName, $option['init'])
            ->incrBy($seqModLabel.'_'.$objectName,$option['step'])
            ->get($seqModLabel.'_'.$objectName)
            ->exec();

        if(isset($respInfo[2])){
            return intval($respInfo[2]);
        }else{
            return $option['init'];
        }
    }

    /**
     * 获取当前序列id
     * @param string $objectName
     * @param array $option
     * @param null $vendorInstance
     * @return int
     */
    public function currentSeq($objectName = '',$option = array(), $vendorInstance = null){
        $option += array(
            'routeKey'=>'Seq',
            'init' => 0,
            //'step' => 1,
        );
        empty($objectName) && $objectName = $this->getRouteKey();
        $seqModLabel = lcfirst($option['routeKey']);

        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $objectName,)
        );
        $respInfo = $vendorInstance->multi()
            ->setnx($seqModLabel.'_'.$objectName, $option['init'])
            ->get($seqModLabel.'_'.$objectName)
            ->exec();

        if(isset($respInfo[1]) && !empty($respInfo[1])){
            return intval($respInfo[1]);
        }else{
            return $option['init'];
        }
    }

    /**
     * 设定序列id
     * @param string $objectName
     * @param array $option
     * @param null $vendorInstance
     * @return int
     */
    public function setSeq($objectName = '',$option = array(), $vendorInstance = null){
        $option += array(
            'routeKey'=>'Seq',
            'init' => 0,
            'step' => 1,
        );
        empty($objectName) && $objectName = $this->getRouteKey();
        $seqModLabel = lcfirst($option['routeKey']);

        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $objectName,)
        );


        $respInfo = $vendorInstance->multi()
            ->set($seqModLabel.'_'.$objectName, $option['init'])
            ->get($seqModLabel.'_'.$objectName)
            ->exec();

        if(isset($respInfo[2])){
            return intval($respInfo[2]);
        }else{
            return $option['init'];
        }
    }



    /**
     * 生成字典下标序列id
     * @param string $objectName
     * @param array $option
     * @param null $vendorInstance
     * @return int
     */
    public function nextDictSeq($objectName = '',$key = '',$option = array(), $vendorInstance = null){
        $option += array(
            'routeKey'=>'Seq',
            'init' => 0,
            'step' => 1,
        );
        empty($objectName) && $objectName = $this->getRouteKey();
        $seqModLabel = lcfirst($option['routeKey']);

        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $objectName,)
        );

        $respInfo = $vendorInstance->multi()
            ->hSetNx($seqModLabel.'_'.$objectName, $key, $option['init'])
            ->hIncrBy($seqModLabel.'_'.$objectName, $key, $option['step'])
            ->exec();
        if(isset($respInfo[1])){
            return intval($respInfo[1]);
        }else{
            return $option['init'];
        }
    }


    /**
     * 读取当前字典下标序列id
     * @param string $objectName
     * @param array $option
     * @param null $vendorInstance
     * @return int
     */
    public function currentDictSeq($objectName = '',$key = '',$option = array(), $vendorInstance = null){
        $option += array(
            'routeKey'=>'Seq',
            'init' => 0,
        );
        empty($objectName) && $objectName = $this->getRouteKey();
        $seqModLabel = lcfirst($option['routeKey']);

        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $objectName,)
        );

        $respInfo = $vendorInstance->multi()
            ->hSetNx($seqModLabel.'_'.$objectName, $key, $option['init'])
            ->hGet($seqModLabel.'_'.$objectName, $key)
            ->exec();
        if(isset($respInfo[1])){
            return intval($respInfo[1]);
        }else{
            return $option['init'];
        }
    }


    /**
     * 读取当前字典下标序列id
     * @param string $objectName
     * @param array $option
     * @param null $vendorInstance
     * @return int
     */
    public function setDictSeq($objectName = '',$key = '',$option = array(), $vendorInstance = null){
        $option += array(
            'routeKey'=>'Seq',
            'init' => 0,
        );
        empty($objectName) && $objectName = $this->getRouteKey();
        $seqModLabel = lcfirst($option['routeKey']);

        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $objectName,)
        );

        $respInfo = $vendorInstance->multi()
            ->hSet($seqModLabel.'_'.$objectName, $key, $option['init'])
            ->exec();
        if(isset($respInfo[1])){
            return intval($respInfo[1]);
        }else{
            return $option['init'];
        }
    }


    public function ping($option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        return $vendorInstance->ping();
    }

    public function echoData($key='', $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->echo($key);
    }

    public function bgrewriteaof($option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        return $vendorInstance->bgrewriteaof();
    }

    public function bgsave($option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        return $vendorInstance->bgsave();
    }

    public function dbSize($option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        return $vendorInstance->dbSize();
    }


    public function flushAll($option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        return $vendorInstance->flushAll();
    }

    public function flushDB($option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        return $vendorInstance->flushDB();
    }

    public function info($option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        return $vendorInstance->info();
    }


    public function lastSave($option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        return $vendorInstance->lastSave();
    }

    public function resetStat($option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        return $vendorInstance->resetStat();
    }

    public function save($option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        return $vendorInstance->save();
    }

    public function slaveof($host=null,$port=null,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        if(!empty($host) && !empty($port)){
            return $vendorInstance->slaveof($host,$port);
        }else{
            return $vendorInstance->slaveof();
        }
    }

    public function time($option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        return $vendorInstance->time();
    }


    public function append($key,$val,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->append($key,$val);
    }

    public function getRange($key,$start,$end,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->getRange($key,$start,$end);
    }

    public function setRange($key,$offset,$value,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->setRange($key,$offset,$value);
    }

    public function strlen($key,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->strlen($key);
    }

    public function getBit($key,$offset,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->getBit($key,$offset);
    }

    public function setBit($key,$offset,$value=0,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->setBit($key,$offset,$value);
    }

    public function bitop($params=array(),$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        return call_user_func_array(array($vendorInstance, 'bitop'), $params);
    }

    public function bitcount($key,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->bitcount($key);
    }

    public function type($key,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->type($key);
    }

    public function getSet($key,$val='',$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->getSet($key,$val);
    }

    public function randomKey($option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        return $vendorInstance->randomKey();
    }

    public function move($key='',$dbidx=0,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->move($key,$dbidx);
    }

    public function select($dbidx=0,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        return $vendorInstance->select($dbidx);
    }


    public function rename($key='',$dstkey='',$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->rename($key,$dstkey);
    }

    public function renameNx($key='',$dstkey='',$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->renameNx($key,$dstkey);
    }

    public function decr($key='',$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->decr($key);
    }

    public function decrBy($key,$step=1,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->decrBy($key,$step);
    }

    public function incr($key='',$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->incr($key);
    }

    public function incrBy($key,$step=1,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->incrBy($key,$step);
    }

    public function incrByFloat($key,$step=1.0,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->incrByFloat($key,$step);
    }

    public function sort($key,$opts=array(),$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->sort($key,$opts);
    }

    public function ttl($key,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->ttl($key);
    }

    public function pttl($key,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->pttl($key);
    }

    public function persist($key,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->persist($key);
    }

    public function setnx($key,$val='',$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->setnx($key,$val);
    }

    public function setex($key,$ttl=3600,$val='',$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->setex($key,$ttl,$val);
    }

    public function delete($key,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->delete($key);
    }

    public function exists($key,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->exists($key);
    }

    public function expire($key,$ttl=0,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->expire($key,$ttl);
    }


    public function setTimeout($key,$ttl=0,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->setTimeout($key,$ttl);
    }


    public function expireAt($key,$ts=0,$option = array(), $vendorInstance = null) {
        $ts<1 && $ts=time();
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->expireAt($key,$ts);
    }

    public function pexpireAt($key,$ts=0,$option = array(), $vendorInstance = null) {
        $ts<1 && $ts=time()*1000;
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->pexpireAt($key,$ts);
    }

    public function pexpire($key,$ttl=0,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->pexpire($key,$ttl);
    }

    public function keys($key,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->keys($key);
    }

    public function object($info='idletime',$key='',$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->object($info,$key);
    }

    public function mGet(array $keys=array(),$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        return $vendorInstance->mGet($keys);
    }

    public function mDel(array $keys=array(),$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        return $vendorInstance->delete($keys);
    }

    public function mset(array $items=array(),$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        return $vendorInstance->mset($items);
    }

    public function msetnx(array $items=array(),$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        return $vendorInstance->msetnx($items);
    }

    public function dump($key='',$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->dump($key);
    }

    public function restore($key,$ttl=0,$val='',$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->restore($key,$ttl,$val);
    }

    public function migrate($host='127.0.0.1',$port=6379,$key='',$dstdb=0,$timeout=3600,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->migrate($host,$port,$key,$dstdb,$timeout);
    }


    public function hSet($key,$hashKey,$value,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->hSet($key,$hashKey,$value);
    }

    public function hSetNx($key,$hashKey,$value,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->hSetNx($key,$hashKey,$value);
    }

    public function hGet($key,$hashKey,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->hGet($key,$hashKey);
    }

    public function hLen($key,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->hLen($key);
    }

    public function hDel($key,$hashKey,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->hDel($key,$hashKey);
    }

    public function hKeys($key,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->hKeys($key);
    }

    public function hVals($key,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->hVals($key);
    }

    public function hGetAll($key,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->hGetAll($key);
    }

    public function hExists($key,$hashKey,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->hExists($key,$hashKey);
    }

    public function hIncrBy($key,$hashKey,$value,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->hIncrBy($key,$hashKey,$value);
    }

    public function hIncrByFloat($key,$hashKey,$value,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->hIncrByFloat($key,$hashKey,$value);
    }


    public function hMSet($key,$hashMap,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->hMSet($key,$hashMap);
    }


    public function hMGet($key,$hashKeys,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->hMGet($key,$hashKeys);
    }

    public function sAdd($key,$vals=array(),$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        if(!is_array($vals)){
            return $vendorInstance->sAdd($key,$vals);
        }else{
            array_unshift($vals, $key);
            return call_user_func_array(array($vendorInstance, 'sAdd'), $vals);
        }
    }

    public function sSize($key,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->sSize($key);
    }

    public function sDiff($keys=array(),$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        return call_user_func_array(array($vendorInstance, 'sDiff'), $keys);
    }

    public function sDiffStore($storekey='',$keys=array(),$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$storekey,)
        );
        array_unshift($keys, $storekey);
        return call_user_func_array(array($vendorInstance, 'sDiffStore'), $keys);
    }

    public function sInter($keys=array(),$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        return call_user_func_array(array($vendorInstance, 'sInter'), $keys);
    }

    public function sInterStore($storekey='',$keys=array(),$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$storekey,)
        );
        array_unshift($keys, $storekey);
        return call_user_func_array(array($vendorInstance, 'sInterStore'), $keys);
    }


    public function sUnion($keys=array(),$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        return call_user_func_array(array($vendorInstance, 'sUnion'), $keys);
    }

    public function sUnionStore($storekey='',$keys=array(),$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$storekey,)
        );
        array_unshift($keys, $storekey);
        return call_user_func_array(array($vendorInstance, 'sUnionStore'), $keys);
    }

    public function sContains($key='',$val='',$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->sContains($key,$val);
    }

    public function sGetMembers($key='',$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->sGetMembers($key);
    }
    public function sPop($key='',$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->sPop($key);
    }
    public function sRandMember($key,$count=0,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        if($count!=0){
            return $vendorInstance->sRandMember($key,$count);
        }else{
            return $vendorInstance->sRandMember($key);
        }
    }

    public function sRemove($key,$vals=array(),$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        if(!is_array($vals)){
            return $vendorInstance->sRemove($key,$vals);
        }else{
            array_unshift($vals, $key);
            return call_user_func_array(array($vendorInstance, 'sRemove'), $vals);
        }
    }

    public function zAdd($key, $score, $value,$option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->zAdd($key, $score, $value);
    }

    public function zCardData($key, $option = array(), $vendorInstance = null) {
        return $this->zSize($key,$option,$vendorInstance);
    }

    public function zSize($key, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->zSize($key);
    }


    public function zCount($key,$start=0,$end=0, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->zCount($key,$start,$end);
    }
    public function zIncrBy($key,$step=1.0,$member, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->zIncrBy($key, $step, $member);
    }

    public function zInter($outkey='',$keys=array(),$weights=array(),$aggfunc='', $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$outkey,)
        );
        $args = array(
            $outkey,
            $keys,
        );
        if(!empty($weights)){
            array_push($args, $weights);
        }
        if(!empty($aggfunc)){
            array_push($args, $aggfunc);
        }
        return call_user_func_array(array($vendorInstance, 'zInter'), $args);
    }

    public function zUnion($outkey='',$keys=array(),$weights=array(),$aggfunc='', $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$outkey,)
        );
        $args = array(
            $outkey,
            $keys,
        );
        if(!empty($weights)){
            array_push($args, $weights);
        }
        if(!empty($aggfunc)){
            array_push($args, $aggfunc);
        }
        return call_user_func_array(array($vendorInstance, 'zUnion'), $args);
    }


    public function zRange($key,$start=0,$end=0,$withscores=false, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->zRange($key,$start,$end,$withscores);
    }

    public function zRevRange($key,$start=0,$end=0,$withscores=false, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->zRevRange($key,$start,$end,$withscores);
    }

    public function zRangeByScore($key,$start=0,$end=0,$params=array(), $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->zRangeByScore($key,$start,$end,$params);
    }

    public function zRevRangeByScore($key,$start=0,$end=0,$params=array(), $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->zRevRangeByScore($key,$start,$end,$params);
    }

    public function zRank($key,$member, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->zRank($key,$member);
    }

    public function zRevRank($key,$member, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->zRevRank($key,$member);
    }


    public function zRem($key,$member, $option = array(), $vendorInstance = null) {
        return $this->zDelete($key,$member,$option,$vendorInstance);
    }

    public function zDelete($key,$member, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->zDelete($key,$member);
    }

    public function zRemRangeByRank($key,$start=0.0,$end=0.0, $option = array(), $vendorInstance = null) {
        return $this->zDeleteRangeByRank($key,$start,$end,$option,$vendorInstance);
    }
    public function zDeleteRangeByRank($key,$start=0.0,$end=0.0, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->zDeleteRangeByRank($key,$start,$end);
    }

    public function zRemRangeByScore($key,$start=0.0,$end=0.0, $option = array(), $vendorInstance = null) {
        return $this->zDeleteRangeByScore($key,$start,$end,$option,$vendorInstance);
    }
    public function zDeleteRangeByScore($key,$start=0.0,$end=0.0, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->zDeleteRangeByScore($key,$start,$end);
    }

    public function zScore($key,$member, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->zScore($key,$member);
    }

    public function blPop($keys=array(),$timeout=10, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        return $vendorInstance->blPop($keys,$timeout);
    }

    public function brPop($keys=array(),$timeout=10, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        return $vendorInstance->brPop($keys,$timeout);
    }

    public function brpoplpush($key,$dstkey,$timeout=10, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->brpoplpush($key,$dstkey,$timeout);
    }

    public function lIndex($key,$index=0, $option = array(), $vendorInstance = null) {
        return $this->lGet($key,$index,$option,$vendorInstance);
    }

    public function lGet($key,$index=0, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->lGet($key,$index);
    }

    public function lInsert($key,$params=array(), $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        array_unshift($params, $key);
        return call_user_func_array(array($vendorInstance, 'lInsert'), $params);
    }
    public function lPop($key, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->lPop($key);
    }

    public function lPush($key, $value, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->lPush($key,$value);
    }

    public function lPushx($key, $value, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->lPushx($key,$value);
    }

    public function lGetRange($key, $start = 0, $end = -1, $option = array(), $vendorInstance = null) {
        return $this->lRange($key,$start,$end,$option,$vendorInstance);
    }

    public function lRange($key, $start = 0, $end = -1, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->lRange($key, $start, $end);
    }

    public function lRemove($key, $value,$count=0, $option = array(), $vendorInstance = null) {
        return $this->lRem($key,$value,$count,$option,$vendorInstance);
    }

    public function lRem($key, $value,$count=0, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->lRem($key,$value,$count);
    }


    public function lSet($key, $index=0, $value='', $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->lSet($key,$index,$value);
    }


    public function listTrim($key, $start=0, $end=-1, $option = array(), $vendorInstance = null) {
        return $this->lTrim($key,$start,$end,$option,$vendorInstance);
    }

    public function lTrim($key, $start=0, $end=-1, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->lTrim($key,$start,$end);
    }
    public function rPop($key, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->rPop($key);
    }

    public function rpoplpush($key,$dstkey, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->rpoplpush($key,$dstkey);
    }

    public function rPush($key, $value, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->rPush($key,$value);
    }

    public function rPushx($key, $value, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->rPushx($key,$value);
    }

    public function lLen($key, $option = array(), $vendorInstance = null) {
        return $this->lSize($key,$option,$vendorInstance);
    }
    public function lSize($key, $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key'=>$key,)
        );
        return $vendorInstance->lSize($key);
    }
    public function publish($channel,$message='', $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        return $vendorInstance->publish($channel,$message);
    }

    public function subscribe($channels=array(),$callback='', $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        return $vendorInstance->subscribe($channels,$callback);
    }

    public function psubscribe($patterns=array(),$callback='', $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        return $vendorInstance->psubscribe($patterns,$callback);
    }


    public function multi($params='', $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        if(!empty($params)){
            return $vendorInstance->multi($params);
        }else{
            return $vendorInstance->multi();
        }
    }

    public function watch($keys=array(), $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        return $vendorInstance->watch($keys);
    }

    public function unwatch($keys=array(), $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array()
        );
        return $vendorInstance->unwatch($keys);
    }


}