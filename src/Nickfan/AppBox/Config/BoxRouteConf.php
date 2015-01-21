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
use Jeremeamia\SuperClosure\SerializableClosure;
use Nickfan\AppBox\Common\BoxConstants;
use Nickfan\AppBox\Common\Exception\FileNotFoundException;
use Nickfan\AppBox\Common\Exception\RuntimeException;
use Nickfan\AppBox\Common\Exception\UnexpectedValueException;
use Nickfan\AppBox\Common\Usercache\BoxUsercacheInterface;
use Nickfan\AppBox\Support\BoxUtil;

class BoxRouteConf implements ArrayAccess {


    protected $userCacheObj = null;

    protected $userCacheTTL = BoxConstants::USERCACHE_TTL_DEFAULT;

    // Include paths
    protected $includePath;

    protected $parsedConf = array();

    /**
     * All of the configuration items.
     *
     * @var array
     */
    protected $itemsConf = array();

    protected $parsedScript = array();

    /**
     * All of the configuration items.
     *
     * @var array
     */
    protected $itemsScript = array();


    /**
     * Create a new configuration repository.
     *
     * @param  \Nickfan\AppBox\Common\Usercache\BoxUsercacheInterface $userCacheObj
     * @param  string $includePath
     * @return void
     */
    public function __construct(BoxUsercacheInterface $userCacheObj = null, $includePath = '') {
        $this->userCacheObj = $userCacheObj;
        $this->includePath = $includePath;
    }

    public static function getVersion() {
        return BoxConstants::VERSION;
    }

    public function getRouteConfByScript($driver, $routeKey, $attributes = array()) {
        $parsedSet = $this->getRouteConfKeySetByScript($driver, $routeKey, $attributes);
        if (is_null($parsedSet)) {
            // got no script
            throw new RuntimeException('got no route script:' . $driver);
            //return $this->getRouteConfInit($driver,$routeKey);
        }
        return $this->getRouteConfSubset($driver, $parsedSet['routeKey'], $parsedSet['group']);
    }

    public function getRouteConfByRouteConfKeySet($driver, $parsedSet) {
        if (is_null($parsedSet)) {
            // got no script
            throw new RuntimeException('got no route script:' . $driver);
            //return $this->getRouteConfInit($driver,$routeKey);
        }
        return $this->getRouteConfSubset($driver, $parsedSet['routeKey'], $parsedSet['group']);
    }

    public function getRouteConfKeySetByScript($driver, $routeKey, $attributes = array()) {
        $routeScriptClosure = $this->getScript($driver);
        if (!is_null($routeScriptClosure)) {
            return call_user_func_array($routeScriptClosure, array($routeKey, $attributes));
        } else {
            return null;
        }
    }

    public function getRouteScriptClosure($driver) {
        return $this->getScript($driver);
    }

    public function getRootConfTree($driver) {
        return $this->getConf($driver . '.' . BoxConstants::CONF_KEY_ROOT, array());
    }

    public function getRootInitConf($driver) {
        return $this->getConf(
            $driver . '.' . BoxConstants::CONF_KEY_ROOT . '.' . BoxConstants::CONF_LABEL_INIT,
            array()
        );
    }

    public function getRouteConfInit($driver, $routeKey) {
        return array_merge(
            $this->getRootInitConf($driver),
            $this->getConf($driver . '.' . $routeKey . '.' . BoxConstants::CONF_LABEL_INIT, array())
        );
    }

    public function getRouteConfSubset($driver, $routeKey, $subset) {
        return array_merge(
            $this->getRouteConfInit($driver, $routeKey),
            $this->getConf($driver . '.' . $routeKey . '.' . $subset, array())
        );
    }

    public function getRouteConfSubtree($driver, $routeKey) {
        $result = $this->getConf($driver . '.' . $routeKey);
        if (is_array($result)) {
            $initConf = $this->getRouteConfInit($driver, $routeKey);
            foreach ($result as $labelKey => $labelRow) {
                $result[$labelKey] = array_merge($initConf, $labelRow);
            }
        }
        return $result;
    }

    public function getRouteConfSubKeys($driver, $routeKey) {
        $result = $this->getConf($driver . '.' . $routeKey);
        if (is_array($result)) {
            return array_keys($result);
        }
        return array();
    }

    public function getIncludePath() {
        return $this->includePath;
    }

    public function setIncludePath($includePath = '') {
        return $this->includePath = $includePath;
    }

    public function setUserCacheObject(BoxUsercacheInterface $userCacheObj, $option = array()) {
        $this->userCacheObj = $userCacheObj;
        $option += array(
            'ttl' => BoxConstants::USERCACHE_TTL_DEFAULT,
            'encode' => 'serialize',
        );
        $this->userCacheObj->setOption($option);
    }

    public function cacheLoad($driver, $isScript = false) {
        if (!is_null($this->userCacheObj)) {
            $path = $this->includePath;
            if ($isScript == true) {
                $file = "{$path}/route/{$driver}.php";
            } else {
                $file = "{$path}/dsn/{$driver}.json";
            }
            return $this->userCacheObj->get($file);
        } else {
            return null;
        }
    }

    public function cacheDel($driver, $isScript = false) {
        if (!is_null($this->userCacheObj)) {
            $path = $this->includePath;
            if ($isScript == true) {
                $file = "{$path}/route/{$driver}.php";
            } else {
                $file = "{$path}/dsn/{$driver}.json";
            }
            return $this->userCacheObj->del($file);
        }
        return null;
    }

    public function cacheSave($driver, $items, $isScript = false) {
        if (!is_null($this->userCacheObj)) {
            $path = $this->includePath;
            if ($isScript == true) {
                $file = "{$path}/route/{$driver}.php";
            } else {
                $file = "{$path}/dsn/{$driver}.json";
            }
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
    public function getConf($key = '', $default = null) {
        list($driver, $item) = $this->parseConfKey($key);
        $this->loadConf($driver);
        return BoxUtil::array_get($this->itemsConf[$driver], $item, $default);
    }

    /**
     * Set a given configuration value.
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function setConf($key, $value) {
        list($driver, $item) = $this->parseConfKey($key);
        $this->loadConf($driver);

        if (is_null($item)) {
            $this->itemsConf[$driver] = $value;
        } else {
            BoxUtil::array_set($this->itemsConf[$driver], $item, $value);
        }
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param  string $key
     * @return bool
     */
    public function hasConf($key) {
        $default = microtime(true);

        return $this->getConf($key, $default) !== $default;
    }


    /**
     * Load the configuration group for the key.
     *
     * @param  string $driver
     * @param  string $namespace
     * @param  string $collection
     * @return void
     */
    protected function loadConf($driver) {
        // If we've already loaded this collection, we will just bail out since we do
        // not want to load it again. Once items are loaded a first time they will
        // stay kept in memory within this class and not loaded from disk again.
        if (isset($this->itemsConf[$driver])) {
            return;
        }

        $items = $this->cacheLoad($driver, false);
        if (!empty($items)) {
            $this->itemsConf[$driver] = $items;
        } else {
            $items = $this->fileLoadConf($driver);
            $this->cacheSave($driver, $items, false);
            $this->itemsConf[$driver] = $items;
        }
    }


    /**
     * Get the specified configuration value.
     *
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public function getScript($key = '', $default = null) {
        list($driver, $item) = $this->parseScriptKey($key);
        $this->loadScript($driver);
        return BoxUtil::array_get($this->itemsScript[$driver], $item, $default);
    }

    /**
     * Set a given configuration value.
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function setScript($key, $value) {
        list($driver, $item) = $this->parseConfKey($key);
        $this->loadConf($driver);

        if (is_null($item)) {
            $this->itemsScript[$driver] = $value;
        } else {
            BoxUtil::array_set($this->itemsScript[$driver], $item, $value);
        }
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param  string $key
     * @return bool
     */
    public function hasScript($key) {

        return !is_null($this->getConf($key));
    }

    /**
     * Load the configuration group for the key.
     *
     * @param  string $driver
     * @param  string $namespace
     * @param  string $collection
     * @return void
     */
    protected function loadScript($driver) {
        // If we've already loaded this collection, we will just bail out since we do
        // not want to load it again. Once items are loaded a first time they will
        // stay kept in memory within this class and not loaded from disk again.
        if (isset($this->itemsScript[$driver])) {
            return;
        }

        $items = $this->cacheLoad($driver, true);
        if (!empty($items)) {
            $this->itemsScript[$driver] = $items;
        } else {
            $items = $this->fileLoadScript($driver);
            $this->cacheSave($driver, $items, true);
            $this->itemsScript[$driver] = $items;
        }
    }

    /**
     * Parse a key into  group, and item.
     *
     * @param  string $key
     * @return array
     */
    public function parseConfKey($key) {
        // If we've already parsed the given key, we'll return the cached version we
        // already have, as this will save us some processing. We cache off every
        // key we parse so we can quickly return it on all subsequent requests.
        if (isset($this->parsedConf[$key])) {
            return $this->parsedConf[$key];
        }

        $segments = explode('.', $key);

        $parsed = $this->parseSegments($segments);

        // Once we have the parsed array of this key's elements, such as its groups
        // and namespace, we will cache each array inside a simple list that has
        // the key and the parsed array for quick look-ups for later requests.
        return $this->parsedConf[$key] = $parsed;
    }

    /**
     * Parse a key into  group, and item.
     *
     * @param  string $key
     * @return array
     */
    public function parseScriptKey($key) {
        // If we've already parsed the given key, we'll return the cached version we
        // already have, as this will save us some processing. We cache off every
        // key we parse so we can quickly return it on all subsequent requests.
        if (isset($this->parsedScript[$key])) {
            return $this->parsedScript[$key];
        }

        $segments = explode('.', $key);

        $parsed = $this->parseSegments($segments);

        // Once we have the parsed array of this key's elements, such as its groups
        // and namespace, we will cache each array inside a simple list that has
        // the key and the parsed array for quick look-ups for later requests.
        return $this->parsedScript[$key] = $parsed;
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
        $driver = $segments[0];

        if (count($segments) == 1) {
            return array($driver, null);
        }

        // If there is more than one segment in this group, it means we are pulling
        // a specific item out of a groups and will need to return the item name
        // as well as the group so we know which item to pull from the arrays.
        else {
            $item = implode('.', array_slice($segments, 1));

            return array($driver, $item);
        }
    }

    /**
     * Load the given configuration group.
     *
     * @param  string $environment
     * @param  string $driver
     * @param  string $namespace
     * @return array
     */
    public function fileLoadConf($driver, $includePath = '') {
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
        $file = "{$path}/dsn/{$driver}.json";

        if (file_exists($file)) {
            $contents = $this->fileGetContent($file);
            $items = json_decode($contents, true);
            $lasterror = json_last_error();
            if ($lasterror !== JSON_ERROR_NONE) {
                throw new UnexpectedValueException(json_last_error_msg(), json_last_error());
            }
        }
        return $items;
    }


    /**
     * Load the given configuration group.
     *
     * @param  string $environment
     * @param  string $driver
     * @param  string $namespace
     * @return array
     */
    public function fileLoadScript($driver, $includePath = '') {
        $scriptClosure = null;

        // First we'll get the root configuration path for the environment which is
        // where all of the configuration files live for that namespace, as well
        // as any environment folders with their specific configuration items.
        if (!empty($includePath)) {
            $this->includePath = $includePath;
        }
        $path = $this->includePath;


        if (is_null($path)) {
            return $scriptClosure;
        }

        // First we'll get the main configuration file for the groups. Once we have
        // that we can check for any environment specific files, which will get
        // merged on top of the main arrays to make the environments cascade.
        $file = "{$path}/route/{$driver}.php";

        if (file_exists($file)) {
            $scriptClosure = $this->fileGetRequire($file);
            if (!is_callable($scriptClosure)) {
                throw new UnexpectedValueException('route script error:' . $driver);
            }
            $scriptClosure = new SerializableClosure($scriptClosure);
        }
        return $scriptClosure;
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
    public function getItemsConf() {
        return $this->itemsConf;
    }

    /**
     * Determine if the given configuration option exists.
     *
     * @param  string $key
     * @return bool
     */
    public function offsetExists($key) {
        return $this->hasConf($key);
    }

    /**
     * Get a configuration option.
     *
     * @param  string $key
     * @return mixed
     */
    public function offsetGet($key) {
        return $this->getConf($key);
    }

    /**
     * Set a configuration option.
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function offsetSet($key, $value) {
        $this->setConf($key, $value);
    }

    /**
     * Unset a configuration option.
     *
     * @param  string $key
     * @return void
     */
    public function offsetUnset($key) {
        $this->setConf($key, null);
    }

}