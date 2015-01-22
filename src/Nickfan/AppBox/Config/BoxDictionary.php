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
use Nickfan\AppBox\Support\BoxUtil;

class BoxDictionary implements ArrayAccess,Serializable,IteratorAggregate{

    const ENCODER_SERIALIZE = 'serialize';
    const ENCODER_JSON = 'json';
    const ENCODER_MSGPACK = 'msgpack';

    protected $parsed = array();

    protected $option = array(
        'packer'=>self::ENCODER_SERIALIZE,
    );

    /**
     * All of the configuration items.
     *
     * @var array
     */
    protected $items = array();

    public function __construct($items=array(),$option=array()){
        $option+=array(
            'packer'=>self::ENCODER_SERIALIZE,
        );
        $this->option = array_merge($this->option,$option);
        $this->setItems($items);
        return $this;
    }

    public static function factory($items=array(),$option=array()){
        $className = get_called_class();
        return new $className($items,$option);
    }

    public function packData($var,$bool=true,$option=array()){
        $option+=array(
            'packer'=>$this->option['packer'],
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

    /**
     * Get all of the configuration items.
     *
     * @return array
     */
    public function getItems() {
        return $this->items;
    }

    public function setItems($items=array()){
        $this->items = $items;
        $this->parsed = array();
    }

    public function cleanup(){
        $this->items = array();
        $this->parsed = array();
    }
    /**
     * Parse a key into  group, and item.
     *
     * @param  string $key
     * @return array
     */
    public function parseKey($key) {
        // If we've already parsed the given key, we'll return the cached version we
        // already have, as this will save us some processing. We cache off every
        // key we parse so we can quickly return it on all subsequent requests.
        if (isset($this->parsed[$key])) {
            return $this->parsed[$key];
        }

        $segments = explode('.', $key);

        $parsed = $this->parseSegments($segments);

        // Once we have the parsed array of this key's elements, such as its groups
        // and namespace, we will cache each array inside a simple list that has
        // the key and the parsed array for quick look-ups for later requests.
        return $this->parsed[$key] = $parsed;
    }

    /**
     * Parse an array of basic segments.
     *
     * @param  array $segments
     * @return array
     */
    protected function parseSegments(array $segments) {
        // The first segment in a basic array will always be the group, so we can go
        // ahead and grab that segment. If there is only one total segment we are
        // just pulling an entire group out of the array and not a single item.
        $group = $segments[0];

        if (count($segments) == 1) {
            return array($group, null);
        }

        // If there is more than one segment in this group, it means we are pulling
        // a specific item out of a groups and will need to return the item name
        // as well as the group so we know which item to pull from the arrays.
        else {
            $item = implode('.', array_slice($segments, 1));

            return array($group, $item);
        }
    }

    /**
     * Get the specified configuration value.
     *
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public function get($key = '', $default = null) {
        list($group, $item) = $this->parseKey($key);
        return isset($this->items[$group])?BoxUtil::array_get($this->items[$group], $item, $default):$default;
    }

    /**
     * Set a given configuration value.
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function set($key, $value) {
        list($group, $item) = $this->parseKey($key);

        if (is_null($item)) {
            $this->items[$group] = $value;
        } else {
            BoxUtil::array_set($this->items[$group], $item, $value);
        }
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param  string $key
     * @return bool
     */
    public function has($key) {
        $default = microtime(true);
        return $this->get($key, $default) !== $default;
    }

    public function exists($key){
        return $this->has($key);
    }
    public function remove($key){
        $this->set($key, null);
    }
    public function keys(){
        return array_keys($this->items);
    }

    public function toArray() {
        return $this->items;
    }

    public function __toString() {
        return $this->packData($this->items,true,$this->option);
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
        $this->set($key, $value);
    }


    public function __get($key){
        return $this->get($key);
    }

    public function __isset($key){
        return $this->has($key);
    }
    public function __unset($key){
        $this->set($key, null);
    }


    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize() {
        return $this->packData($this->items,true,$this->option);
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
        $this->items = $this->packData($serialized,false,$this->option);
    }

    /**
     * Determine if the given configuration option exists.
     *
     * @param  string $key
     * @return bool
     */
    public function offsetExists($key) {
        return $this->has($key);
    }

    /**
     * Get a configuration option.
     *
     * @param  string $key
     * @return mixed
     */
    public function offsetGet($key) {
        return $this->get($key);
    }

    /**
     * Set a configuration option.
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function offsetSet($key, $value) {
        $this->set($key, $value);
    }

    /**
     * Unset a configuration option.
     *
     * @param  string $key
     * @return void
     */
    public function offsetUnset($key) {
        $this->set($key, null);
    }

}
