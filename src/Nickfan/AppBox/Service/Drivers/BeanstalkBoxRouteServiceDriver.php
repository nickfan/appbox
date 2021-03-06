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


use Nickfan\AppBox\Common\Exception\RuntimeException;
use Nickfan\AppBox\Service\BoxBaseRouteServiceDriver;
use Nickfan\AppBox\Service\BoxRouteServiceDriverInterface;

class BeanstalkBoxRouteServiceDriver extends BoxBaseRouteServiceDriver implements BoxRouteServiceDriverInterface {

    //protected static $driverKey = 'beanstalk';

    protected static function encodeData($srcData){
        return msgpack_pack($srcData);

    }
    protected static function decodeData($encodedData=''){
        return msgpack_unpack($encodedData);
    }
    public function statsTube($tubeName='default', $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $tubeName,)
        );
        return $vendorInstance->statsTube($tubeName);
    }


    public function watchOnly($tubeName='default', $option = array(), $vendorInstance = null) {
        $option += array();
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $tubeName,)
        );
        return $vendorInstance->watchOnly($tubeName);
    }


    /**
     * 处理任务数据的回调方法
     * @param function $callback
     * @param resource $vendorInstance
     * @param array $option
     * @throws MyRuntimeException
     */
    public static function processJobData($callback,$vendorInstance,$option=array()){
        $option += array(
            'decode'=>TRUE,
            'delete'=>TRUE,
            'release'=>FALSE,
            'delay'=>10,
            'priority'=>1024,
            'timeout'=>NULL,
        );
        $retData = NULL;

            //echo 'proceed job data'.PHP_EOL;
            //var_dump($vendorInstance);
            $job = $vendorInstance->reserve($option['timeout']);
            //var_dump($job);
            if($job){
                if($option['decode']==TRUE){
                    $arg= self::decodeData($job->getData());
                }else{
                    $arg= $job->getData();
                }
                if(!is_callable($callback)){
                    throw new RuntimeException('process data error');
                }
                $retData = call_user_func($callback, $arg);
                if(!$retData){
                    if($option['release']==TRUE)
                    {
                        $vendorInstance->release($job,$option['priority'],$option['delay']);
                    }
                    throw new RuntimeException('process data error');
                }
                if($option['delete']==TRUE){
                    $vendorInstance->delete($job);
                }
            }else{
                $arg = $job;
            }

        return $retData;
    }

    /**
     * 写入一个任务
     * @param string $tubeName
     * @param string $jobContent
     * @param array $option
     */
    public function putJob($jobData=null,$tubeName='default', $option = array(), $vendorInstance = null){
        $option += array(
            'encode'=> TRUE,
        );
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $tubeName,)
        );
        $vendorInstance->useTube($tubeName)
            ->put($option['encode']==TRUE?self::encodeData($jobData):$jobData);
    }


    /**
     * 从任务队列中读出一个任务的数据
     * @param string $tubeName
     * @param array $option
     */
    public function getJobData($tubeName='default',$option=array(), $vendorInstance=null){
        $option += array(
            'decode'=> TRUE,
        );
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $tubeName,)
        );

        if($tubeName!='default'){
            $job = $vendorInstance->watch($tubeName)->ignore('default')->reserve();
        }else{
            $job = $vendorInstance->watch($tubeName)->reserve();
        }
        if($job){
            if($option['decode']==TRUE){
                return self::decodeData($job->getData());
            }else{
                return $job->getData();
            }
        }
        return $job;
    }


    /**
     * 从任务队列中读出一个任务
     * @param string $tubeName
     * @param array $option
     */
    public function getJob($tubeName='default',$option=array(), $vendorInstance=null){
        $option += array(
            'ignore'=>'default',
        );
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $tubeName,)
        );

        if($tubeName!='default'){
            $job = $vendorInstance->watch($tubeName)->ignore($option['ignore'])->reserve();
        }else{
            $job = $vendorInstance->watch($tubeName)->reserve();
        }
        return $job;
    }

    /**
     * 删除一个任务
     * @param Pheanstalk_Job $job
     * @param string $tubeName
     * @param array $option
     */
    public function delJob($job,$tubeName='default',$option=array(), $vendorInstance=null){
        $option += array(
        );
        list($vendorInstance, $option) = $this->getVendorInstanceSet(
            $option,
            $vendorInstance,
            array('key' => $tubeName,)
        );
        $vendorInstance->delete($job);
    }

}