<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-11 11:07
 *
 */


namespace Nickfan\BoxApp\BoxDispatcher;

use \NoahBuscher\Macaw\Macaw as Route;

class DefaultBoxDispatcher implements BoxDispatcherInterface{

    const DEFAULT_INDEX = 'index.php';
    const DEFAULT_DOMAIN = 'Localhost';
    const DEFAULT_PACKAGE = 'Index';
    const DEFAULT_MODULE = 'Index';
    const DEFAULT_ACTION = 'Index';
    const DEFAULT_NAMESPACE_WEB = '\\Nickfan\\BoxApp\\BoxController';
    const DEFAULT_NAMESPACE_CLI = '\\Nickfan\\BoxApp\\BoxCommand';

    protected static $app;

    protected static $instance = null;

    protected $baseNamespace = '';

    // 域名类中是否包含端口号
    protected $domainIncludePort = false;

    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct($app) {
        if (!is_null($app)) {
            static::setApp($app);
        }
        $this->init();
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     */
    private function __clone() {
    }

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     *
     * @return void
     */
    private function __wakeup() {
    }

    /**
     * Returns the *Singleton* instance of this class.
     *
     * @staticvar Singleton $instance The *Singleton* instances of this class.
     *
     * @return Singleton The *Singleton* instance.
     */
    public static function getInstance($app = null) {
        if (null === self::$instance) {
            self::$instance = new static($app);
        }
        return self::$instance;
    }

    public function instance(){
        return self::$instance;
    }
    public static function setApp($app) {
        static::$app = $app;
    }

    public static function getApp() {
        return static::$app;
    }

    private static function verifyLabel($label = '') {
        if (preg_match('/[a-z0-9\_]+/i', $label) || strlen($label) == 0) {
            return true;
        } else {
            return false;
        }
    }

    protected $domain = null;
    protected $current_uri = '';
    protected $host = null;
    protected $pkg = null;
    protected $mod = null;
    protected $act = null;
    protected $segments = array();
    protected static $app_root = '';
    protected static $data_root = '';
    protected static $public_root = '';

    public function getBaseNamespace() {
        return $this->baseNamespace;
    }

    public function setBaseNamespace($baseNamespace = '') {
        $this->baseNamespace = $baseNamespace;
    }

    public function getDomainIncludePort() {
        return $this->domainIncludePort;
    }

    public function setDomainIncludePort($domainIncludePort = true) {
        $this->domainIncludePort = $domainIncludePort;
    }

    public function getDomain(){
        return $this->domain;
    }
    public function getCurrentUri(){
        return $this->current_uri;
    }

    public function getPkg(){
        return $this->pkg;
    }
    public function getMod(){
        return $this->mod;
    }
    public function getAct(){
        return $this->act;
    }
    public function getSegments(){
        return $this->segments;
    }
    public function getHost(){
        return $this->host;
    }

    /**
     * Determine if we are running in the console.
     *
     * @return bool
     */
    public function runningInConsole(){
        return php_sapi_name() == 'cli';
    }

    protected function parseDomain($domain='',$port = '80'){
        $domain_dir = $domain;
        if(strlen($domain)==0){
            $domain_dir = 'localhost';
        }elseif(preg_match('/^\d+.\d+.\d+.\d+(:?:\d+|)$/',$domain)){
            $domain_dir = 'ip'.$domain;
        }else{
            $domain_set = explode(':',$domain_dir);
            $domain_dir = $domain_set[0];
            if(count($domain_set)>1){
                $port = $domain_set[1];
            }
        }
        if($this->getDomainIncludePort()==true){
            if($port!='80'){
                $domain_dir.=':'.$port;
            }
        }
        return ucfirst(str_replace(array('.',':'),array('Dot','Colon'),$domain_dir));
    }

    protected function parseCurrentUri4Web(){
        $current_uri = '';
        if (!isset($_SERVER['PATH_INFO']) && isset($_SERVER['REQUEST_URI'])) {
            $_SERVER['PATH_INFO'] = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        }
        if (isset($_SERVER['PATH_INFO']) AND $_SERVER['PATH_INFO'])
        {
            $current_uri = $_SERVER['PATH_INFO'];
        }
        elseif (isset($_SERVER['ORIG_PATH_INFO']) AND $_SERVER['ORIG_PATH_INFO'])
        {
            $current_uri = $_SERVER['ORIG_PATH_INFO'];
        }
        elseif (isset($_SERVER['PHP_SELF']) AND $_SERVER['PHP_SELF'])
        {
            $current_uri = $_SERVER['PHP_SELF'];
        }
        if (($strpos_fc = strpos($current_uri, self::DEFAULT_INDEX)) !== FALSE)
        {
            $current_uri = (string) substr($current_uri, $strpos_fc + strlen(self::DEFAULT_INDEX));
        }
        $current_uri = trim($current_uri, '/');
        if($current_uri !== ''){
            preg_replace('#//+#', '/', $current_uri);
        }
        return $current_uri;
    }


    public function initCli($args=array()){
        $params = $args;
        $params += array(
            'domain' => '',
            'pkg' => 'index',
            'mod' => 'index',
            'act' => 'index',
            'segments' => array(),
        );
        if(isset($_SERVER['argv'])){
            $argvars = $_SERVER['argv'];
            if(count($argvars)>1){
                array_shift($argvars);
                $req_uri = array_shift($argvars);
                if(strpos($req_uri,'//')===false){
                    $req_uri = 'ssh://'.$req_uri;
                }
                $this->current_uri = $req_uri;
                $req_urlinfo = parse_url($req_uri);
                $domain = isset($req_urlinfo['host'])?$req_urlinfo['host']:'localhost';
                $domain_set = explode(':',$domain);
                $domain = $domain_set[0];
                $port = isset($req_urlinfo['port'])?$req_urlinfo['port']:(count($domain_set)>1?$domain_set[1]:'80');

                $this->host = $port=='80'?$domain:$domain.':'.$port;

                $this->domain =  $this->parseDomain($domain,$port);

                $gotAction = false;
                $gotSegments = false;
                $req_uri_path = isset($req_urlinfo['path'])?$req_urlinfo['path']:'/';
                if(preg_match('/^\/([a-z0-9\_]+)(?:\/([a-z0-9\_]+)(?:\/([a-z0-9\_]+)|)|)(?:\/(.*)|)$/i',$req_uri_path,$matches)){
                    $gotAction = true;
                    $params['pkg'] = trim($matches[1]);
                    $params['mod'] = isset($matches[2])?trim($matches[2]):'index';
                    $params['act'] = isset($matches[3])?trim($matches[3]):'index';
                    if(isset($matches[4]) && strlen($matches[4])>0){
                        $gotSegments = true;
                        $params['segments'] = explode('/',trim($matches[4],'/'));
                    }
                }elseif(preg_match('/^\/([a-z0-9\_]+)(?:\.([a-z0-9\_]+)(?:\.([a-z0-9\_]+)|)|)(?:\/(.*)|)$/i',$req_uri_path,$matches)){
                    $gotAction = true;
                    $params['pkg'] = trim($matches[1]);
                    $params['mod'] = isset($matches[2])?trim($matches[2]):'index';
                    $params['act'] = isset($matches[3])?trim($matches[3]):'index';
                    if(isset($matches[4]) && strlen($matches[4])>0){
                        $gotSegments = true;
                        $params['segments'] = explode('/',trim($matches[4],'/'));
                    }
                }
                $req_uri_query = isset($req_urlinfo['query'])?$req_urlinfo['query']:'';
                $req_uri_queryDict = array();
                if(!empty($req_uri_query)){
                    parse_str($req_uri_query, $req_uri_queryDict);
                }
                if($gotAction!=true){
                    isset($req_uri_queryDict['pkg']) && $params['pkg'] = trim($req_uri_queryDict['pkg']);
                    isset($req_uri_queryDict['mod']) && $params['mod'] = trim($req_uri_queryDict['mod']);
                    isset($req_uri_queryDict['act']) && $params['act'] = trim($req_uri_queryDict['act']);
                    $querysegments = array();
                    isset($req_uri_queryDict['seg']) && $querysegments = is_array($req_uri_queryDict['seg'])?array_map('trim',$req_uri_queryDict['seg']):array(trim($req_uri_queryDict['seg']));
                    if(count($querysegments)>0){
                        foreach ($querysegments as $line=>$rowvar) {
                            $params['segments'][] = $rowvar;
                        }
                    }
                }
                if(!empty($req_uri_queryDict)){
                    $queryParams = $req_uri_queryDict;
                    if(isset($queryParams['pkg'])){ unset($queryParams['pkg']);}
                    if(isset($queryParams['mod'])){ unset($queryParams['mod']);}
                    if(isset($queryParams['act'])){ unset($queryParams['act']);}
                    if(isset($queryParams['seg'])){ unset($queryParams['seg']);}
                    if(!empty($queryParams)){
                        array_push($params['segments'],$queryParams);
                    }
                }
                if(count($argvars)>0){
                    foreach ($argvars as $line=>$rowvar) {
                        $params['segments'][] = $rowvar;
                    }
                }
            }else{
                $this->domain =  $this->parseDomain($params['domain']);
            }
        }else{
            $this->domain =  $this->parseDomain($params['domain']);
        }
        $this->pkg = ucfirst(self::verifyLabel($params['pkg']) ? $params['pkg'] : 'index');
        $this->mod = ucfirst(self::verifyLabel($params['mod']) ? $params['mod'] : 'index');
        $this->act = lcfirst(self::verifyLabel($params['act']) ? $params['act'] : 'index');
        if(isset($params['segments']) && !empty($params['segments'])){
            $this->segments = $params['segments'];
        }
    }

    public function initWeb($args = array()) {
        $params = $args;
        $params += array(
            'domain' => '',
            'pkg' => 'index',
            'mod' => 'index',
            'act' => 'index',
            'segments' => array(),
            'app_root' => isset(static::$app['path']) ? static::$app['path'] : '',
            'data_root' => isset(static::$app['path.storage']) ? static::$app['path.storage'] : '',
            'public_root' => isset(static::$app['path.public']) ? static::$app['path.public'] : '',
            'basenamespace' => static::$app['boxconf']->get('app.namespace.web'),
        );

        if(strlen($params['domain'])==0){
            $domain = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR']:''));
            $domain_set = explode(':',$domain);
            $domain = $domain_set[0];
            $port = count($domain_set)>1?$domain_set[1]:(isset($_SERVER["HTTP_PORT"]) ? $_SERVER['HTTP_PORT'] : '80');
        }else{
            $domain_set = explode(':',$params['domain']);
            $domain = $domain_set[0];
            $port = count($domain_set)>1?$domain_set[1]:'80';
        }
        $this->host = $port=='80'?$domain:$domain.':'.$port;
        $this->domain = $this->parseDomain($domain,$port);

        $this->current_uri = $this->parseCurrentUri4Web();

        $gotAction = false;
        if(isset($_SERVER['REQUEST_URI'])){
            $req_uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            if(preg_match('/^\/([a-z0-9\_]+)(?:\/([a-z0-9\_]+)(?:\/([a-z0-9\_]+)|)|)(?:\/(.*)|)$/i',$req_uri_path,$matches)){
                $gotAction = true;
                $params['pkg'] = trim($matches[1]);
                $params['mod'] = isset($matches[2])?trim($matches[2]):'index';
                $params['act'] = isset($matches[3])?trim($matches[3]):'index';
                if(isset($matches[4]) && strlen($matches[4])>0){
                    $params['segments'] = explode('/',trim($matches[4],'/'));
                }
            }elseif(preg_match('/^\/([a-z0-9\_]+)(?:\.([a-z0-9\_]+)(?:\.([a-z0-9\_]+)|)|)(?:\/(.*)|)$/i',$req_uri_path,$matches)){
                $gotAction = true;
                $params['pkg'] = trim($matches[1]);
                $params['mod'] = isset($matches[2])?trim($matches[2]):'index';
                $params['act'] = isset($matches[3])?trim($matches[3]):'index';
                if(isset($matches[4]) && strlen($matches[4])>0){
                    $params['segments'] = explode('/',trim($matches[4],'/'));
                }
            }
        }
        /* try get method */
        if($gotAction!=true){
            isset($_GET['pkg']) && $params['pkg'] = trim($_GET['pkg']);
            isset($_GET['mod']) && $params['mod'] = trim($_GET['mod']);
            isset($_GET['act']) && $params['act'] = trim($_GET['act']);
            isset($_GET['seg']) && $params['segments'] = is_array($_GET['seg'])?array_map('trim',$_GET['seg']):array(trim($_GET['seg']));
        }
        $this->pkg = ucfirst(self::verifyLabel($params['pkg']) ? $params['pkg'] : 'index');
        $this->mod = ucfirst(self::verifyLabel($params['mod']) ? $params['mod'] : 'index');
        $this->act = lcfirst(self::verifyLabel($params['act']) ? $params['act'] : 'index');
        if(isset($params['segments']) && !empty($params['segments'])){
            $segments = array();
            foreach($params['segments'] as $line=>$seg){
                if(self::verifyLabel($seg)){
                    $segments[$line] = $seg;
                }else{
                    $segments[$line] = '';
                }
            }
            $this->segments = $segments;
        }
    }

    public function init($args=array()){

        $params = $args;
        if($this->runningInConsole()){
            $params += array(
                'domain' => '',
                'pkg' => 'index',
                'mod' => 'index',
                'act' => 'index',
                'segments' => array(),
                'app_root' => static::$app['path'],
                'data_root' => static::$app['path.storage'],
                'public_root' => static::$app['path.public'],
                'basenamespace' => static::$app['boxconf']->get('app.namespace.web'),
            );
            $this->initCli($args);
        }else{
            $params += array(
                'domain' => '',
                'pkg' => 'index',
                'mod' => 'index',
                'act' => 'index',
                'segments' => array(),
                'app_root' => static::$app['path'],
                'data_root' => static::$app['path.storage'],
                'public_root' => static::$app['path.public'],
                'basenamespace' => static::$app['boxconf']->get('app.namespace.cli'),
            );
            $this->initWeb($args);
        }
        if(!empty($params['basenamespace'])){
            $this->baseNamespace = $params['basenamespace'];
        }
        self::$app_root = $params['app_root'];
        self::$data_root = $params['data_root'];
        self::$public_root = $params['public_root'];
    }

    public function runCall($baseNamespace = null) {
        if(empty($baseNamespace)){
            if(empty($this->baseNamespace)){
                if($this->runningInConsole()){
                    $baseNamespace = self::DEFAULT_NAMESPACE_CLI;
                }else{
                    $baseNamespace = self::DEFAULT_NAMESPACE_WEB;
                }
            }else{
                $baseNamespace = $this->baseNamespace;
            }
        }

        $controllername = $baseNamespace.'\\'. $this->domain . '\\' . $this->pkg . '\\' . $this->mod;
        if (class_exists($controllername)) {
            $controllerObj = new $controllername(self::getInstance());
            if(method_exists($controllerObj,$this->act)){
                if(empty($this->segments)){
                    return call_user_func(array($controllerObj, $this->act));
                }else{
                    return call_user_func_array(array($controllerObj, $this->act),$this->segments);
                }
            }else{
                throw new \BadMethodCallException('undefined action:'.$controllername.' -> '.$this->act, 500);
            }
        } else {
            throw new \BadMethodCallException('undefined controller:'.$controllername, 500);
        }
    }
    public function run($baseNamespace = null){
        if($this->runningInConsole()){
            return $this->runCall($baseNamespace);
        }else{
            require static::$data_root.'/conf/routes.php';
            Route::error(function() use ($baseNamespace) {
                return $this->runCall($baseNamespace);
            });
            Route::dispatch();
            //return $this->runCall($baseNamespace);
        }
    }
}
