<?php
/**
 * Description
 *
 * @project AppBox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-19 10:46
 *
 */


namespace Nickfan\AppBox\Foundation;

use Nickfan\AppBox\Container\Container;
use Nickfan\AppBox\Support\Util;

class AppBox extends Container {
    /**
     * Quick debugging of any variable. Any number of parameters can be set.
     *
     * @return  string
     */
    public static function debug() {
        if (func_num_args() === 0 || func_num_args() === 1) {
            return null;
        }
        // Get params
        $params = func_get_args();
        $printBool = boolval(array_shift($params));
        $output = array();
        foreach ($params as $var) {
            $output[] = '(' . gettype($var) . ') ' . var_export($var, true) . '';
        }
        if (php_sapi_name() == 'cli') {
            if ($printBool == true) {
                print(implode("\n", $output));
            } else {
                return implode("\n", $output);
            }
        } else {
            if ($printBool == true) {
                print('<pre>' . implode("</pre>\n<pre>", $output) . '</pre>');
            } else {
                return '<pre>' . implode("</pre>\n<pre>", $output) . '</pre>';
            }
        }
        return null;
    }

    /**
     * All of the registered service providers.
     *
     * @var array
     */
    protected $serviceProviders = array();

    /**
     * The names of the loaded service providers.
     *
     * @var array
     */
    protected $loadedProviders = array();

    /**
     * The deferred services and their providers.
     *
     * @var array
     */
    protected $deferredServices = array();

    /**
     * Create a new Illuminate application instance.
     *
     * @param  \Illuminate\Http\Request
     * @return void
     */
    public function __construct() {
        $this->registerBaseBindings();

    }


    /**
     * Register the basic bindings into the container.
     *
     * @return void
     */
    protected function registerBaseBindings() {
        $this->instance('Nickfan\AppBox\Container\Container', $this);
    }


    /**
     * Bind the installation paths to the application.
     *
     * @param  array $paths
     * @return void
     */
    public function bindInstallPaths(array $paths) {
        $this->instance('path', realpath($paths['app']));

        // Here we will bind the install paths into the container as strings that can be
        // accessed from any point in the system. Each path key is prefixed with path
        // so that they have the consistent naming convention inside the container.
        foreach (Util::array_except($paths, array('app')) as $key => $value) {
            $this->instance("path.{$key}", realpath($value));
        }
    }

    /**
     * Force register a service provider with the application.
     *
     * @param  \Nickfan\AppBox\Support\ServiceProvider|string $provider
     * @param  array $options
     * @return \Nickfan\AppBox\Support\ServiceProvider
     */
    public function forgeRegister($provider, $options = array()) {
        return $this->register($provider, $options, true);
    }

    /**
     * Register a service provider with the application.
     *
     * @param  \Nickfan\AppBox\Support\ServiceProvider|string $provider
     * @param  array $options
     * @param  bool $force
     * @return \Nickfan\AppBox\Support\ServiceProvider
     */
    public function register($provider, $options = array(), $force = false) {
        if ($registered = $this->getRegistered($provider) && !$force) {
            return $registered;
        }

        // If the given "provider" is a string, we will resolve it, passing in the
        // application instance automatically for the developer. This is simply
        // a more convenient way of specifying your service provider classes.
        if (is_string($provider)) {
            $provider = $this->resolveProviderClass($provider);
        }

        $provider->register();

        // Once we have registered the service we will iterate through the options
        // and set each of them on the application so they will be available on
        // the actual loading of the service objects and for developer usage.
        foreach ($options as $key => $value) {
            $this[$key] = $value;
        }

        $this->markAsRegistered($provider);

        // If the application has already booted, we will call this boot method on
        // the provider class so it has an opportunity to do its boot logic and
        // will be ready for any usage by the developer's application logics.
        if ($this->booted) {
            $provider->boot();
        }

        return $provider;
    }

    /**
     * Get the registered service provider instance if it exists.
     *
     * @param  \Nickfan\AppBox\Support\ServiceProvider|string $provider
     * @return \Nickfan\AppBox\Support\ServiceProvider|null
     */
    public function getRegistered($provider) {
        $name = is_string($provider) ? $provider : get_class($provider);

        if (array_key_exists($name, $this->loadedProviders)) {
            return Util::array_first(
                $this->serviceProviders,
                function ($key, $value) use ($name) {
                    return get_class($value) == $name;
                }
            );
        }
    }

    /**
     * Resolve a service provider instance from the class name.
     *
     * @param  string $provider
     * @return \Nickfan\AppBox\Support\ServiceProvider
     */
    public function resolveProviderClass($provider) {
        return new $provider($this);
    }

    /**
     * Mark the given provider as registered.
     *
     * @param  \Illuminate\Support\ServiceProvider
     * @return void
     */
    protected function markAsRegistered($provider) {
        $class = get_class($provider);
        $this->serviceProviders[] = $provider;
        $this->loadedProviders[$class] = true;
    }

    /**
     * Load and boot all of the remaining deferred providers.
     *
     * @return void
     */
    public function loadDeferredProviders() {
        // We will simply spin through each of the deferred providers and register each
        // one and boot them if the application has booted. This should make each of
        // the remaining services available to this application for immediate use.
        foreach ($this->deferredServices as $service => $provider) {
            $this->loadDeferredProvider($service);
        }

        $this->deferredServices = array();
    }

    /**
     * Load the provider for a deferred service.
     *
     * @param  string $service
     * @return void
     */
    protected function loadDeferredProvider($service) {
        $provider = $this->deferredServices[$service];

        // If the service provider has not already been loaded and registered we can
        // register it with the application and remove the service from this list
        // of deferred services, since it will already be loaded on subsequent.
        if (!isset($this->loadedProviders[$provider])) {
            $this->registerDeferredProvider($provider, $service);
        }
    }

    /**
     * Register a deferred provider and service.
     *
     * @param  string $provider
     * @param  string $service
     * @return void
     */
    public function registerDeferredProvider($provider, $service = null) {
        // Once the provider that provides the deferred service has been registered we
        // will remove it from our local list of the deferred services with related
        // providers so that this container does not try to resolve it out again.
        if ($service) {
            unset($this->deferredServices[$service]);
        }
        $this->register($instance = new $provider($this));
    }

    /**
     * Resolve the given type from the container.
     *
     * (Overriding Container::make)
     *
     * @param  string $abstract
     * @param  array $parameters
     * @return mixed
     */
    public function make($abstract, $parameters = array()) {
        $abstract = $this->getAlias($abstract);

        if (isset($this->deferredServices[$abstract])) {
            $this->loadDeferredProvider($abstract);
        }

        return parent::make($abstract, $parameters);
    }

    /**
     * Get the service providers that have been loaded.
     *
     * @return array
     */
    public function getLoadedProviders() {
        return $this->loadedProviders;
    }

    /**
     * Set the application's deferred services.
     *
     * @param  array $services
     * @return void
     */
    public function setDeferredServices(array $services) {
        $this->deferredServices = $services;
    }

    /**
     * Determine if the given service is a deferred service.
     *
     * @param  string $service
     * @return bool
     */
    public function isDeferredService($service) {
        return isset($this->deferredServices[$service]);
    }


    /**
     * Register the core class aliases in the container.
     *
     * @return void
     */
    public function registerCoreContainerAliases() {
        $aliases = array(
            'app' => 'Nickfan\AppBox\Foundation\AppBox',
            'config' => 'Nickfan\AppBox\Config\Repository',
            'datarouteconf' => 'Nickfan\AppBox\Config\DataRouteConf',
            'datarouteinstance' => 'Nickfan\AppBox\Instance\DataRouteInstance',
        );

        foreach ($aliases as $key => $alias) {
            $this->alias($key, $alias);
        }
    }

    /**
     * Dynamically access application services.
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key) {
        return $this[$key];
    }

    /**
     * Dynamically set application services.
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function __set($key, $value) {
        $this[$key] = $value;
    }

} 