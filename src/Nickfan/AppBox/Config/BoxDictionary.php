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



namespace Nickfan\AppBox\Config;

use ArrayAccess;
use Traversable;
use Serializable;
use IteratorAggregate;
use ArrayIterator;

class BoxSettings implements ArrayAccess,Serializable,IteratorAggregate{
    const TYPE_AUTO = 'auto';
    const TYPE_BOOL = 'bool';
    const TYPE_INT = 'int';
    const TYPE_FLOAT = 'float';
    const TYPE_STRING = 'string';
    const TYPE_BINARY = 'binary';
    const TYPE_OBJECT = 'object';

    const KEYTYPE_NONE = 'none';
    const KEYTYPE_ID = 'id';
    const KEYTYPE_IDSTR = 'idstr';
    const KEYTYPE_UUID = 'uuid';

    const ENCODER_JSON = 'json';
    const ENCODER_MSGPACK = 'msgpack';
    const ENCODER_SERIALIZE = 'serialize';

    const DO_VERSION_PROP = '_version';

    const DO_VERSION = '0';

    protected static $dataEncoder = self::ENCODER_SERIALIZE;
    protected static $strictMode = true;

    protected $propSetDefault = array(
        'key'=>self::KEYTYPE_NONE,          // keytype
        'type'=>self::TYPE_AUTO,          // prop var type
        'default'=>0,                      // default value
        'length'=>null,                     // data length limit
        'enabled'=>true,                   // enable this field
    );
    protected $props = array(
        'id'=>array(
            'key'=>self::KEYTYPE_ID,          // keytype
            'type'=>self::TYPE_INT,          // prop var type
            'default'=>0,                      // default value
            'length'=>null,                     // data length limit
            'enabled'=>false,                   // enable this field
        ),
        'idstr'=>array(
            'key'=>self::KEYTYPE_IDSTR,          // keytype
            'type'=>self::TYPE_STRING,          // prop var type
            'default'=>'',                      // default value
            'length'=>null,                     // data length limit
            'enabled'=>false,                   // enable this field
        ),
//        '_id'=>array(
//            'key'=>self::KEYTYPE_UUID,          // keytype
//            'type'=>self::TYPE_STRING,          // prop var type
//            'default'=>'',                      // default value
//            'length'=>null,                     // data length limit
//            'enabled'=>false,                   // enable this field
//        ),
        '_version'=>array(
            'key'=>self::KEYTYPE_NONE,          // keytype
            'type'=>self::TYPE_STRING,          // prop var type
            'default'=>self::DO_VERSION,                      // default value
            'length'=>null,                     // data length limit
            'enabled'=>false,                   // enable this field
        ),
    );
    protected $idPropKey = null;
    protected $idStrPropKey = null;

    protected $keys = array();
    protected $data = array();

    public function packData($var,$bool=true,$option=array()){
        $option+=array(
            'packer'=>self::$dataEncoder,
        );
        switch($option['packer']){
            case self::ENCODER_JSON:
                if($bool==true){
                    return json_encode($var);
                }else{
                    return json_decode($var,true);
                }
                break;
            case self::ENCODER_MSGPACK:
                if($bool==true){
                    return msgpack_pack($var);
                }else{
                    return msgpack_unpack($var);
                }
                break;
            case self::ENCODER_SERIALIZE:
            default:
                if($bool==true){
                    return serialize($var);
                }else{
                    return unserialize($var);
                }
                break;
        }
    }

    public function keys(){
        if(empty($this->keys)){
            if(self::$strictMode == true){
                //$keys = array_keys($this->props);
                // keep prop keys order
                $keys = array();
                foreach ($this->props as $propKey=>$propSet) {
                    if(!isset($propSet['enabled']) || $propSet['enabled']==true){
                        $keys[$propKey] = $propKey;
                    }
                }
                $this->keys = $keys;
            }else{
                //$keys = array_unique(array_keys($this->props),array_keys($this->data));
                // keep prop keys order
                $keys = array();
                foreach ($this->data as $propKey=>$propVal) {
                    $keys[$propKey] = $propKey;
                }
                foreach ($this->props as $propKey=>$propSet) {
                    if(!isset($keys[$propKey]) && (!isset($propSet['enabled']) || $propSet['enabled']==true)){
                        $keys[$propKey] = $propKey;
                    }
                }
                $this->keys = $keys;
            }
        }
        return $this->keys;
    }

    protected function getIdPropKey(){
        if(is_null($this->idPropKey)){
            foreach ($this->props as $propKey=>$propSet) {
                if(isset($propSet['key']) && $propSet['key']==self::KEYTYPE_ID){
                    if(!isset($propSet['enabled']) || $propSet['enabled']==true){
                        $this->idPropKey = $propKey;
                    }else{
                        $this->idPropKey = '';
                    }
                    break;
                }
            }
        }
        return $this->idPropKey;
    }
    protected function setIdPropKey($key=null){
        return $this->idPropKey=$key;
    }
    protected function getIdStrPropKey(){
        if(is_null($this->idStrPropKey)){
            foreach ($this->props as $propKey=>$propSet) {
                if(isset($propSet['key']) && $propSet['key']==self::KEYTYPE_IDSTR){
                    if(!isset($propSet['enabled']) || $propSet['enabled']==true){
                        $this->idStrPropKey = $propKey;
                    }else{
                        $this->idStrPropKey = '';
                    }
                    break;
                }
            }
        }
        return $this->idStrPropKey;
    }
    protected function setIdStrPropKey($key=null){
        return $this->idStrPropKey=$key;
    }

    public function get($key){
        if(array_key_exists($key,$this->props) && (!isset($this->props[$key]['enabled']) || $this->props[$key]['enabled']==true)){
            if(isset($this->props[$key]['key']) && $this->props[$key]['key']==self::KEYTYPE_IDSTR){
                $getIdPropKey = $this->getIdPropKey();
                if(!empty($getIdPropKey)){
                    if(array_key_exists($getIdPropKey,$this->data)){
                        return strval($this->data[$getIdPropKey]);
                    }
                }
            }

            if(array_key_exists($key,$this->data)){
                if(!is_null($this->data[$key])){
                    if(isset($this->props[$key]['type']) && $this->props[$key]['type']==self::TYPE_OBJECT){
                        return $this->packData($this->data[$key],false);
                    }else{
                        return $this->data[$key];
                    }
                }else{
                    return null;
                }
            }else{
                return isset($this->props[$key]['default'])?$this->props[$key]['default']:null;
            }
        }else{
            if(self::$strictMode == false && array_key_exists($key,$this->data)){
                return $this->data[$key];
            }else{
                return null;
            }

        }
    }


    public function set($key, $value){
        if(array_key_exists($key,$this->props) && (!isset($this->props[$key]['enabled']) || $this->props[$key]['enabled']==true)){
            if(isset($this->props[$key]['type'])){
                switch($this->props[$key]['type']){
                    case self::TYPE_BOOL:
                        $this->data[$key] = boolval($value);
                        break;
                    case self::TYPE_INT:
                        $this->data[$key] = intval($value);
                        break;
                    case self::TYPE_FLOAT:
                        $this->data[$key] = floatval($value);
                        break;
                    case self::TYPE_STRING:
                        $this->data[$key] = strval($value);
                        break;
                    case self::TYPE_BINARY:
                        $this->data[$key] = $value;
                        break;
                    case self::TYPE_OBJECT:
                        $this->data[$key] = !is_scalar($value)?$this->packData($value,true):$value;
                        break;
                    case self::TYPE_AUTO:
                    default:
                        if(is_bool($value)){
                            $this->data[$key] = boolval($value);
                        }elseif(is_float($value)){
                            $this->data[$key] = floatval($value);
                        }elseif(is_numeric($value)){
                            $this->data[$key] = intval($value);
                        }elseif(is_string($value)){
                            $this->data[$key] = strval($value);
                        }elseif(is_object($value) || is_array($value)){
                            $this->data[$key] = $this->packData($value,true);
                        }else{
                            $this->data[$key] = $value;
                        }
                        break;
                }
            }else{
                if(is_bool($value)){
                    $this->data[$key] = boolval($value);
                }elseif(is_float($value)){
                    $this->data[$key] = floatval($value);
                }elseif(is_numeric($value)){
                    $this->data[$key] = intval($value);
                }elseif(is_string($value)){
                    $this->data[$key] = strval($value);
                }elseif(is_object($value) || is_array($value)){
                    $this->data[$key] = $this->packData($value,true);
                }else{
                    $this->data[$key] = $value;
                }
            }
            if(isset($this->props[$key]['key'])){
                if($this->props[$key]['key']==self::KEYTYPE_ID){
                    $this->setIdPropKey($key);
                    $getIdStrPropKey = $this->getIdStrPropKey();
                    if(!empty($getIdStrPropKey)){
                        $this->data[$getIdStrPropKey] = strval($value);
                    }
                }elseif($this->props[$key]['key']==self::KEYTYPE_IDSTR){
                    $this->setIdStrPropKey($key);
                }
            }
        }elseif(self::$strictMode == false){
            if(is_bool($value)){
                $this->data[$key] = boolval($value);
            }elseif(is_float($value)){
                $this->data[$key] = floatval($value);
            }elseif(is_numeric($value)){
                $this->data[$key] = intval($value);
            }elseif(is_string($value)){
                $this->data[$key] = strval($value);
            }elseif(is_object($value) || is_array($value)){
                $this->data[$key] = $this->packData($value,true);
            }else{
                $this->data[$key] = $value;
            }
        }
    }

    public function exists($key){
        if(array_key_exists($key,$this->props) && (!isset($this->props[$key]['enabled']) || $this->props[$key]['enabled']==true)){
            return true;
        }else{
            if(self::$strictMode == false && array_key_exists($key,$this->data)){
                return true;
            }else{
                return false;
            }
        }
    }
    public function remove($key){
        if(array_key_exists($key,$this->data)){
            unset($this->data[$key]);
        }
    }

    public function setProps($setProps=array(),$option=array()){
        $option+=array(
            'init'=>true, // init all props.
        );
        $changedKeys = array();
        if(!empty($setProps)){
            foreach ($setProps as $key => $value) {
                $this->set($key,$value);
                $changedKeys[$key] = $key;
            }
        }
        if($option['init']==true){
            $remainKeys = array_diff(array_keys($this->props),$changedKeys);
            if(!empty($remainKeys)){
                foreach ($remainKeys as $key) {
                    if(!isset($this->props[$key]['enabled']) || $this->props[$key]['enabled']==true){
                        if(isset($this->props[$key]['default'])){
                            $this->data[$key] = $this->props[$key]['default'];
                        }else{
                            if(isset($this->props[$key]['type'])){
                                switch($this->props[$key]['type']){
                                    case self::TYPE_BOOL:
                                        $this->data[$key] = false;
                                        break;
                                    case self::TYPE_INT:
                                        $this->data[$key] = 0;
                                        break;
                                    case self::TYPE_FLOAT:
                                        $this->data[$key] = 0.0;
                                        break;
                                    case self::TYPE_STRING:
                                        $this->data[$key] = '';
                                        break;
                                    case self::TYPE_BINARY:
                                        $this->data[$key] = '';
                                        break;
                                    case self::TYPE_OBJECT:
                                        $this->data[$key] = $this->packData('',true);
                                        break;
                                    case self::TYPE_AUTO:
                                    default:
                                        $this->data[$key] = null;
                                        break;
                                }
                            }else{
                                $this->data[$key] = null;
                            }
                        }
                    }
                }
            }
        }
    }


    public function upgradeObject($objectData = null){
        $retData = null;
        if(!empty($objectData)){
            $this->setProps($objectData,array('init'=>true));
            $retData = $this->toArray();
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
    public function extraStruct(){
        $data = $this->data;
        foreach ($data as $propKey=>$propVal) {
            if(isset($this->props[$propKey]) && isset($this->props[$propKey]['type']) && $this->props[$propKey]['type']==self::TYPE_OBJECT){
                $data[$propKey] = !empty($propVal)?$this->packData($propVal,false):$propVal;
            }
        }
        return $data;
    }

    public function __toString() {
        return $this->packData($this->data,true);
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
        return $this->packData($this->data,true);
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
        $this->data = $this->packData($serialized,false);
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
