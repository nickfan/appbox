<?php
/**
 * Description
 *
 * @project appbox
 * @package 
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-07-02 14:09
 *
 */



namespace Nickfan\AppBox\DataObject;

use ArrayAccess;
use Traversable;
use Serializable;
use IteratorAggregate;

abstract class BaseDataObject implements ArrayAccess,Serializable,IteratorAggregate{
    //protected static $dataObjectUuidPropKey = '_id';
    protected static $dataObjectUuidPropKey = '';
    protected static $dataObjectUuidPropValue = '';
    protected static $dataObjectIdPropKey = 'id';
    protected static $dataObjectIdPropValue = null;
    protected static $dataObjectIdStrPropKey = 'idstr';
    protected static $dataObjectIdStrPropValue = '';

    protected static $dataObjectVersionPropKey = '_version';
    protected static $dataObjectVersionPropValue = 0;

    protected $data = array();

    public function keys(){
        $keys = array_keys($this->data);
        $predefinedKeys = array(static::$dataObjectUuidPropKey,static::$dataObjectIdPropKey,static::$dataObjectIdStrPropKey,static::$dataObjectVersionPropKey);
        foreach($predefinedKeys as $predefinedKey){
            if($predefinedKey!='' && !in_array($predefinedKey,$predefinedKeys)){
                $keys[] = $predefinedKey;
            }
        }
        return $keys;
    }
    public function get($key){
        if($key==static::$dataObjectIdPropKey){
            if(array_key_exists($key,$this->data)){
                if(is_numeric($this->data[$key])){
                    return intval($this->data[$key]);
                }else{
                    return $this->data[$key];
                }
            }else{
                return static::$dataObjectIdPropValue;
            }
        }elseif($key==static::$dataObjectIdStrPropKey){
            if(array_key_exists($key,$this->data)){
                return strval($this->data[$key]);
            }elseif(static::$dataObjectIdStrPropKey!=''){
                if(array_key_exists(static::$dataObjectIdPropKey,$this->data)){
                    return !is_null($this->data[static::$dataObjectIdPropKey])?strval($this->data[static::$dataObjectIdPropKey]):static::$dataObjectIdStrPropValue;
                }else{
                    return static::$dataObjectIdStrPropValue;
                }
            }else{
                return static::$dataObjectIdStrPropValue;
            }
        }else{
            if(array_key_exists($key,$this->data)){
                return $this->data[$key];
            }else{
                return null;
            }
        }
    }


    public function set($key, $value){
        if($key==static::$dataObjectIdPropKey){
            if(is_numeric($value)){
                $this->data[$key] = intval($value);
            }else{
                $this->data[$key] = $value;
            }
            if(static::$dataObjectIdStrPropKey!='' && isset($this->data[static::$dataObjectIdStrPropKey])){
                $this->data[static::$dataObjectIdStrPropKey] = strval($value);
            }
        }else{
            $this->data[$key] = $value;
        }
    }

    public function exists($key){
        if(array_key_exists($key,$this->data)){
            return true;
        }elseif($key!='' && in_array($key,array(static::$dataObjectUuidPropKey,static::$dataObjectIdPropKey,static::$dataObjectIdStrPropKey,static::$dataObjectVersionPropKey))){
            return true;
        }
        return false;
    }
    public function remove($key){
        if(array_key_exists($key,$this->data)){
            unset($this->data[$key]);
        }
    }

    public function setProps($setProps=array()){
        if(!empty($setProps)){
            foreach ($setProps as $key => $value) {
                if(array_key_exists($key,$this->data)){
                    if($key==static::$dataObjectIdPropKey){
                        if(is_numeric($value)){
                            $this->data[$key] = intval($value);
                        }else{
                            $this->$key = $value;
                        }
                        if(static::$dataObjectIdStrPropKey!='' && isset($this->data[static::$dataObjectIdStrPropKey])){
                            $this->data[static::$dataObjectIdStrPropKey] = strval($value);
                        }
                    }else{
                        $this->$key = $value;
                    }
                }else{
                    if($key==static::$dataObjectIdPropKey){
                        if(is_numeric($value)){
                            $this->data[$key] = intval($value);
                        }else{
                            $this->$key = $value;
                        }
                        if(static::$dataObjectIdStrPropKey!='' && isset($this->data[static::$dataObjectIdStrPropKey])){
                            $this->data[static::$dataObjectIdStrPropKey] = strval($value);
                        }
                    }elseif($key==static::$dataObjectIdStrPropKey){
                        $this->data[static::$dataObjectIdStrPropKey] = strval($value);
                    }elseif($key==static::$dataObjectVersionPropKey){
                        $this->data[static::$dataObjectVersionPropKey] = is_numeric($value)?intval($value):$value;
                    }elseif($key==static::$dataObjectUuidPropKey){
                        $this->data[static::$dataObjectUuidPropKey] = $value;
                    }
                }
            }
        }
        if(static::$dataObjectVersionPropKey!='' && !array_key_exists(static::$dataObjectVersionPropKey,$this->data)){
            $this->data[static::$dataObjectVersionPropKey] = is_numeric(static::$dataObjectVersionPropValue)?intval(static::$dataObjectVersionPropValue):static::$dataObjectVersionPropValue;
        }
        if(static::$dataObjectIdPropKey!='' && !array_key_exists(static::$dataObjectIdPropKey,$this->data)){
            $this->data[static::$dataObjectIdPropKey] = static::$dataObjectIdPropValue;
        }
        if(static::$dataObjectIdStrPropKey!='' && !array_key_exists(static::$dataObjectIdStrPropKey,$this->data)){
            $this->data[static::$dataObjectIdStrPropKey] = static::$dataObjectIdStrPropValue;
        }
        if(static::$dataObjectUuidPropKey!='' && !array_key_exists(static::$dataObjectUuidPropKey,$this->data)){
            $this->data[static::$dataObjectUuidPropKey] = static::$dataObjectUuidPropValue;
        }
    }


    public function upgradeObject($objectData = NULL){
        $retData = NULL;
        if(!empty($objectData)){
            $retData = $this->toArray();
            // 未版本管理的数据
            if(!array_key_exists(static::$dataObjectVersionPropKey, $objectData)){
                foreach ($objectData as $srcKey=>$oval){
                    if(array_key_exists($srcKey, $retData) && $srcKey!=static::$dataObjectVersionPropKey){
                        $retData[$srcKey]=$oval;
                    }elseif($srcKey==static::$dataObjectIdPropKey || $srcKey==static::$dataObjectIdStrPropKey){
                        $retData[$srcKey]=$oval;
                    }
                }
            }else{
                foreach ($objectData as $srcKey=>$oval){
                    if(array_key_exists($srcKey, $retData) && $srcKey!=static::$dataObjectVersionPropKey){
                        $retData[$srcKey]=$oval;
                    }elseif($srcKey==static::$dataObjectIdPropKey || $srcKey==static::$dataObjectIdStrPropKey){
                        $retData[$srcKey]=$oval;
                    }
                }
            }
            if(static::$dataObjectUuidPropKey!='' && array_key_exists(static::$dataObjectUuidPropKey, $objectData)){
                $retData[static::$dataObjectUuidPropKey] = $objectData[static::$dataObjectUuidPropKey];
            }
        }
        return $retData;
    }

    public static function factory($defaultProps=array()){
        $className = get_called_class();
        return new $className($defaultProps);
    }


    public function toArray() {
        return $this->data;
    }
    public function __toString() {
        return json_encode($this->data);
    }

    public function __construct($setProps=array()){
        $this->setProps($setProps);
        return $this;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator() {
        return new ArrayIterator($this->data);
    }


    public function __set($key, $value){
        return $this->set($key, $value);
    }


    public function __get($key){
        return $this->get($key);
    }

    public function __isset($key){
        return $this->exists($key);
    }
    public function __unset($key){
        return $this->remove($key);
    }

    /**
     * @param array $data
     */
    public function setData($data) {
        $this->data = $data;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize() {
        return serialize($this->data);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     */
    public function unserialize($serialized) {
        $this->data = unserialize($serialized);
    }

    /**
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset) {
        return $this->exists($offset);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset) {
        return $this->get($offset);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value) {
        return $this->set($offset,$value);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset) {
        return $this->remove($offset);
    }
}
