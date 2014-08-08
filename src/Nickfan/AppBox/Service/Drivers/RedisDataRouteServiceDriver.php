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


use Nickfan\AppBox\Service\BaseDataRouteServiceDriver;
use Nickfan\AppBox\Service\DataRouteServiceDriverInterface;

class RedisDataRouteServiceDriver extends BaseDataRouteServiceDriver implements DataRouteServiceDriverInterface {

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


}