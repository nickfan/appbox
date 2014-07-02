<?php
/**
 * Description
 *
 * @project servroute
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-05 17:26
 *
 */

namespace Nickfan\AppBox\Config;

use ArrayAccess;
use Nickfan\AppBox\Common\AppConstants;
use Nickfan\AppBox\Common\Exception\FileNotFoundException;
use Nickfan\AppBox\Common\Usercache\UsercacheInterface;
use Nickfan\AppBox\Support\Util;

class Repository implements ArrayAccess {


    protected $userCacheObj = null;

    protected $userCacheTTL = AppConstants::USERCACHE_TTL_DEFAULT;

    // Include paths
    protected $includePath;

    protected $parsed = array();

    /**
     * All of the configuration items.
     *
     * @var array
     */
    protected $items = array();

    /**
     * Create a new configuration repository.
     *
     * @param  \Nickfan\AppBox\Common\Usercache\UsercacheInterface $userCacheObj
     * @param  string $includePath
     * @return void
     */
    public function __construct(UsercacheInterface $userCacheObj = null, $includePath = '') {
        $this->userCacheObj = $userCacheObj;
        $this->includePath = $includePath;
    }

    public static function getVersion() {
        return AppConstants::VERSION;
    }

    public function getIncludePath() {
        return $this->includePath;
    }

    public function setIncludePath($includePath = '') {
        return $this->includePath = $includePath;
    }

    public function setUserCacheObject(UsercacheInterface $userCacheObj) {
        $this->userCacheObj = $userCacheObj;
        $this->userCacheObj->setOption(array('ttl' => AppConstants::USERCACHE_TTL_DEFAULT,));
    }

    public function cacheLoad($group) {
        if (!is_null($this->userCacheObj)) {
            $path = $this->includePath;
            $file = "{$path}/{$group}.php";
            return $this->userCacheObj->get($file);
        } else {
            return null;
        }
    }

    public function cacheDel($group) {
        if (!is_null($this->userCacheObj)) {
            $path = $this->includePath;
            $file = "{$path}/{$group}.php";
            return $this->userCacheObj->del($file);
        }
        return null;
    }

    public function cacheSave($group, $items) {
        if (!is_null($this->userCacheObj)) {
            $path = $this->includePath;
            $file = "{$path}/{$group}.php";
            return $this->userCacheObj->set($file, $items);
        }
        return null;
    }

    public function cacheFlush() {
        if (!is_null($this->userCacheObj)) {
            return $this->userCacheObj->flush();
        }
        return null;
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
        $this->load($group);
        return Util::array_get($this->items[$group], $item, $default);
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
        $this->load($group);

        if (is_null($item)) {
            $this->items[$group] = $value;
        } else {
            Util::array_set($this->items[$group], $item, $value);
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


    /**
     * Load the configuration group for the key.
     *
     * @param  string $group
     * @param  string $namespace
     * @param  string $collection
     * @return void
     */
    protected function load($group) {
        // If we've already loaded this collection, we will just bail out since we do
        // not want to load it again. Once items are loaded a first time they will
        // stay kept in memory within this class and not loaded from disk again.
        if (isset($this->items[$group])) {
            return;
        }

        $items = $this->cacheLoad($group);
        if (!empty($items)) {
            $this->items[$group] = $items;
        } else {
            $items = $this->fileLoad($group);
            $this->cacheSave($group, $items);
            $this->items[$group] = $items;
        }
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
     * Load the given configuration group.
     *
     * @param  string $environment
     * @param  string $group
     * @param  string $namespace
     * @return array
     */
    public function fileLoad($group, $includePath = '') {
        $items = array();

        // First we'll get the root configuration path for the environment which is
        // where all of the configuration files live for that namespace, as well
        // as any environment folders with their specific configuration items.
        if (!empty($includePath)) {
            $this->includePath = $includePath;
        }
        $path = $this->includePath;


        if (is_null($path)) {
            return $items;
        }

        // First we'll get the main configuration file for the groups. Once we have
        // that we can check for any environment specific files, which will get
        // merged on top of the main arrays to make the environments cascade.
        $file = "{$path}/{$group}.php";

        if (file_exists($file)) {
            $items = $this->fileGetRequire($file);
        }
        return $items;
    }

    public function fileGetRequire($path) {
        if (is_file($path)) {
            return require $path;
        }
        throw new FileNotFoundException("File does not exist at path {$path}");
    }

    public function fileGetContent($path) {
        if (is_file($path)) {
            return file_get_contents($path);
        }
        throw new FileNotFoundException("File does not exist at path {$path}");
    }

    /**
     * Get all of the configuration items.
     *
     * @return array
     */
    public function getItems() {
        return $this->items;
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