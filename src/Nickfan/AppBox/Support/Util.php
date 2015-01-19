<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2014-06-16 15:15
 *
 */

namespace Nickfan\AppBox\Support;

!defined('APPBOX_DATAPACKER') && define('APPBOX_DATAPACKER', function_exists('msgpack_pack') ? 1 : 0);

class Util {

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
     * Return the given object. Useful for chaining.
     *
     * @param  mixed $object
     * @return mixed
     */
    public static function with($object) {
        return $object;
    }

    /**
     * Return the default value of the given value.
     *
     * @param  mixed $value
     * @return mixed
     */
    public static function value($value) {
        return $value instanceof \Closure ? $value() : $value;
    }

    /**
     * Replace a given value in the string sequentially with an array.
     *
     * @param  string $search
     * @param  array $replace
     * @param  string $subject
     * @return string
     */
    public static function str_replace_array($search, array $replace, $subject) {
        foreach ($replace as $value) {
            $subject = preg_replace('/' . $search . '/', $value, $subject, 1);
        }

        return $subject;
    }

    /**
     * Replace a given pattern with each value in the array in sequentially.
     *
     * @param  string $pattern
     * @param  array $replacements
     * @param  string $subject
     * @return string
     */
    public static function preg_replace_sub($pattern, &$replacements, $subject) {
        return preg_replace_callback(
            $pattern,
            function ($match) use (&$replacements) {
                return array_shift($replacements);

            },
            $subject
        );
    }


    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param  mixed $target
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public static function data_get($target, $key, $default = null) {
        if (is_array($target)) {
            return self::array_get($target, $key, $default);
        } elseif (is_object($target)) {
            return self::object_get($target, $key, $default);
        } else {
            throw new \InvalidArgumentException("Array or object must be passed to data_get.");
        }
    }

    /**
     * Get the class "basename" of the given object / class.
     *
     * @param  string|object $class
     * @return string
     */
    public static function class_basename($class) {
        $class = is_object($class) ? get_class($class) : $class;

        return basename(str_replace('\\', '/', $class));
    }


    /**
     * Filter the array using the given Closure.
     *
     * @param  array $array
     * @param  \Closure $callback
     * @return array
     */
    public static function array_where($array, \Closure $callback) {
        $filtered = array();

        foreach ($array as $key => $value) {
            if (call_user_func($callback, $key, $value)) {
                $filtered[$key] = $value;
            }
        }

        return $filtered;
    }


    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param  array $array
     * @param  string $key
     * @param  mixed $value
     * @return array
     */
    public static function array_set(&$array, $key, $value) {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = array();
            }

            $array =& $array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    /**
     * Get a value from the array, and remove it.
     *
     * @param  array $array
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public static function array_pull(&$array, $key, $default = null) {
        $value = self::array_get($array, $key, $default);

        self::array_forget($array, $key);

        return $value;
    }

    /**
     * Pluck an array of values from an array.
     *
     * @param  array $array
     * @param  string $value
     * @param  string $key
     * @return array
     */
    public static function array_pluck($array, $value, $key = null) {
        $results = array();

        foreach ($array as $item) {
            $itemValue = is_object($item) ? $item->{$value} : $item[$value];

            // If the key is "null", we will just append the value to the array and keep
            // looping. Otherwise we will key the array using the value of the key we
            // received from the developer. Then we'll return the final array form.
            if (is_null($key)) {
                $results[] = $itemValue;
            } else {
                $itemKey = is_object($item) ? $item->{$key} : $item[$key];

                $results[$itemKey] = $itemValue;
            }
        }

        return $results;
    }

    /**
     * Get a subset of the items from the given array.
     *
     * @param  array $array
     * @param  array $keys
     * @return array
     */
    public static function array_only($array, $keys) {
        return array_intersect_key($array, array_flip((array)$keys));
    }

    /**
     * Get an item from an object using "dot" notation.
     *
     * @param  object $object
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public static function object_get($object, $key, $default = null) {
        if (is_null($key) || trim($key) == '') {
            return $object;
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_object($object) || !isset($object->{$segment})) {
                return self::value($default);
            }

            $object = $object->{$segment};
        }

        return $object;
    }

    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  array $array
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public static function array_get($array, $key, $default = null) {
        if (is_null($key)) {
            return $array;
        }

        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return self::value($default);
            }

            $array = $array[$segment];
        }

        return $array;
    }

    /**
     * Remove an array item from a given array using "dot" notation.
     *
     * @param  array $array
     * @param  string $key
     * @return void
     */
    public static function array_forget(&$array, $key) {
        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (!isset($array[$key]) || !is_array($array[$key])) {
                return;
            }

            $array =& $array[$key];
        }

        unset($array[array_shift($keys)]);
    }

    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @param  array $array
     * @return array
     */
    public static function array_flatten($array) {
        $return = array();

        array_walk_recursive(
            $array,
            function ($x) use (&$return) {
                $return[] = $x;
            }
        );

        return $return;
    }

    /**
     * Return the last element in an array passing a given truth test.
     *
     * @param  array $array
     * @param  Closure $callback
     * @param  mixed $default
     * @return mixed
     */
    public static function array_last($array, $callback, $default = null) {
        return self::array_first(array_reverse($array), $callback, $default);
    }

    /**
     * Return the first element in an array passing a given truth test.
     *
     * @param  array $array
     * @param  Closure $callback
     * @param  mixed $default
     * @return mixed
     */
    public static function array_first($array, $callback, $default = null) {
        foreach ($array as $key => $value) {
            if (call_user_func($callback, $key, $value)) {
                return $value;
            }
        }

        return self::value($default);
    }

    /**
     * Fetch a flattened array of a nested array element.
     *
     * @param  array $array
     * @param  string $key
     * @return array
     */
    public static function array_fetch($array, $key) {
        foreach (explode('.', $key) as $segment) {
            $results = array();

            foreach ($array as $value) {
                $value = (array)$value;

                $results[] = $value[$segment];
            }

            $array = array_values($results);
        }

        return array_values($results);
    }

    /**
     * Get all of the given array except for a specified array of items.
     *
     * @param  array $array
     * @param  array $keys
     * @return array
     */
    public static function array_except($array, $keys) {
        return array_diff_key($array, array_flip((array)$keys));
    }

    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param  array $array
     * @param  string $prepend
     * @return array
     */
    public static function array_dot($array, $prepend = '') {
        $results = array();

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $results = array_merge($results, self::array_dot($value, $prepend . $key . '.'));
            } else {
                $results[$prepend . $key] = $value;
            }
        }

        return $results;
    }

    /**
     * Divide an array into two arrays. One with keys and the other with values.
     *
     * @param  array $array
     * @return array
     */
    public static function array_divide($array) {
        return array(array_keys($array), array_values($array));
    }


    /**
     * Build a new array using a callback.
     *
     * @param  array $array
     * @param  \Closure $callback
     * @return array
     */
    public static function array_build($array, \Closure $callback) {
        $results = array();

        foreach ($array as $key => $value) {
            list($innerKey, $innerValue) = call_user_func($callback, $key, $value);

            $results[$innerKey] = $innerValue;
        }

        return $results;
    }


    /**
     * Add an element to an array if it doesn't exist.
     *
     * @param  array $array
     * @param  string $key
     * @param  mixed $value
     * @return array
     */
    public static function array_add($array, $key, $value) {
        if (!isset($array[$key])) {
            $array[$key] = $value;
        }

        return $array;
    }





    /**
     * 获取路径数据的迭代对象
     * @param string $dataDirPath
     * @param string $filterRegx
     * @return RegexIterator
     *
     * @example
     * 帖子附件
     * $filterRegx = '/^.+\/d('.$queryDataId.')\.([0-9]+)\.(.+)\.bin$/i';
     * 用户头像
     * $filterRegx = '/^.+\/u('.$queryDataId.')\.(photo(?:_s|_m|_l|))\.pic\.bin$/i';
     * 用户签名
     * $filterRegx = '/^.+\/u('.$queryDataId.')\.(sign(?:[0-9a-z_]+|))\.pic\.bin$/i';
     * 用户附件
     * $filterRegx = '/^.+\/u('.$queryDataId.')_(.+)\.up$/i';
     */
    public static function getPathDataIteratorObject($dataDirPath = '', $filterRegx = ''){
        return new RegexIterator(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dataDirPath,FilesystemIterator::SKIP_DOTS)),$filterRegx, RecursiveRegexIterator::GET_MATCH);
    }

    /**
     * 遍历对象
     * @param null $object
     * @return array|bool|float|int|null|string
     */
    public static function traverseObj($object=NULL){
        $retData = NULL;
        if(is_scalar($object) || is_null($object)){
            $retData = $object;
        }elseif(is_array($object)){
            $retData = array();
            if(!empty($object)){
                foreach($object as $key=>$val){
                    $retData[$key] = call_user_func(__METHOD__,$val);
                }
            }
        }else{
            $retData = array();
            if(!empty($object)){
                foreach($object as $key=>$val){
                    $retData[$key] = call_user_func(__METHOD__,$val);
                }
            }
        }
        return $retData;
    }

    /**
     * 转换json_encode后的escape掉的unicode \uxxxx 为原utf-8字符
     * @param unknown $str
     * @return mixed|string
     */
    public static function unescapeunicodestr($str){
        return preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
            function($matches) {
                return mb_convert_encoding(pack('H*', $matches[1]), 'UTF-8', 'UTF-16');
            }, $str);
    }
    /**
     * vbscript 的timer函数
     * @return string
     */
    public static function vbscriptTimer(){
        return sprintf("%0.2f",microtime(TRUE)-strtotime(date('Y-m-d 00:00:00')));
    }
    /**
     * 获取服务器host名称
     * @return string
     */
    public static function getServerHostname(){
        return gethostname();
    }
    /**
     * 构建数据的hash值
     * @param mixed $requestVar
     * @param array $option
     */
    public static function getDataHashKey($requestVar=NULL,array $option=array()){
        $option += array('hash' => 'md5', 'serialize' => APPBOX_DATAPACKER == 1 ? 'msgpack_pack' : 'serialize',);
        if(is_scalar($requestVar)){
            return $option['hash']($requestVar);
        }else{
            return $option['hash']($option['serialize']($requestVar));
        }
    }

    /**
     * Generate an ID, constructed using:
     *   a 4-byte value representing the seconds since the Unix epoch,
     *   a 3-byte machine identifier,
     *   a 2-byte process id, and
     *   a 3-byte counter, starting with a random value.
     * Just like a MongoId string.
     *
     * @link http://www.luokr.com/p/16
     * @link http://docs.mongodb.org/manual/reference/object-id/
     * @return string 24 hexidecimal characters
     */
    function generate_id_hex(){
        static $i = 0;
        $i OR $i = mt_rand(1, 0x7FFFFF);

        return sprintf("%08x%06x%04x%06x",
            /* 4-byte value representing the seconds since the Unix epoch. */
            time() & 0xFFFFFFFF,

            /* 3-byte machine identifier.
             *
             * On windows, the max length is 256. Linux doesn't have a limit, but it
             * will fill in the first 256 chars of hostname even if the actual
             * hostname is longer.
             *
             * From the GNU manual:
             * gethostname stores the beginning of the host name in name even if the
             * host name won't entirely fit. For some purposes, a truncated host name
             * is good enough. If it is, you can ignore the error code.
             *
             * crc32 will be better than Times33. */
            crc32(substr((string)gethostname(), 0, 256)) >> 16 & 0xFFFFFF,

            /* 2-byte process id. */
            getmypid() & 0xFFFF,

            /* 3-byte counter, starting with a random value. */
            $i = $i > 0xFFFFFE ? 1 : $i + 1
        );
    }

    /**
     * 返回变量的压缩的字符串
     * @param string $packedvar
     * @return mixed
     */
    public static function datapack($unpackedvar = NULL) {
        if (APPBOX_DATAPACKER == 1) {
            return msgpack_pack($unpackedvar);
        } else {
            return serialize($unpackedvar);
        }
    }

    /**
     * 返回变量的解压缩还原后的内容
     * @param string $packedvar
     * @return mixed
     */
    public static function dataunpack($packedvar = NULL) {
        if (APPBOX_DATAPACKER == 1) {
            return msgpack_unpack($packedvar);
        } else {
            return unserialize($packedvar);
        }
    }

    /**
     * 转换br标签回newline
     * @param unknown $string
     * @return mixed
     */
    public static function br2nl($string,$convertparagraph=TRUE){
        $string = preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
        if($convertparagraph==TRUE){
            $string = preg_replace('/\<\/p(\s*)?\>(\s*)?\<p(\s*)?\>/i', "\n", $string);
            $string = preg_replace('/\<\/?p(\s*)?\>/i', "\n", $string);
        }
        return $string;
    }

    /**
     * Build the absolute URI based on supplied URI and parameters.
     *
     * @param $uri
     * An absolute URI.
     * @param $params  query,fragment
     * Parameters to be append as GET.
     *
     * @return
     * An absolute URI with supplied parameters.
     *
     */
    public static function mergeUriQuery($uri, $queryData = array())
    {
        $parse_url = parse_url($uri);
        if(!empty($queryData)){
            if(!isset($parse_url["query"])){
                $parse_url["query"] = http_build_query($queryData);
            }else{
                parse_str($uri, $exQueryData);
                $exQueryData = array_merge($exQueryData,$queryData);
                $parse_url["query"] = http_build_query($exQueryData);
            }
        }
        // Put humpty dumpty back together
        return
            ((isset($parse_url["scheme"])) ? $parse_url["scheme"] . "://" : "")
            . ((isset($parse_url["user"])) ? $parse_url["user"]
                . ((isset($parse_url["pass"])) ? ":" . $parse_url["pass"] : "") . "@" : "")
            . ((isset($parse_url["host"])) ? $parse_url["host"] : "")
            . ((isset($parse_url["port"])) ? ":" . $parse_url["port"] : "")
            . ((isset($parse_url["path"])) ? $parse_url["path"] : "")
            . ((isset($parse_url["query"])) ? "?" . $parse_url["query"] : "")
            . ((isset($parse_url["fragment"])) ? "#" . $parse_url["fragment"] : "")
            ;
    }

    /**
     * Build the absolute URI based on supplied URI and parameters.
     *
     * @param $uri
     * An absolute URI.
     * @param $params  query,fragment
     * Parameters to be append as GET.
     *
     * @return
     * An absolute URI with supplied parameters.
     *
     */
    public static function buildUri($uri, $params)
    {
        $parse_url = parse_url($uri);

        // Add our params to the parsed uri
        foreach ( $params as $k => $v ) {
            if (isset($parse_url[$k])) {
                $parse_url[$k] .= "&" . http_build_query($v);
            } else {
                $parse_url[$k] = http_build_query($v);
            }
        }

        // Put humpty dumpty back together
        return
            ((isset($parse_url["scheme"])) ? $parse_url["scheme"] . "://" : "")
            . ((isset($parse_url["user"])) ? $parse_url["user"]
                . ((isset($parse_url["pass"])) ? ":" . $parse_url["pass"] : "") . "@" : "")
            . ((isset($parse_url["host"])) ? $parse_url["host"] : "")
            . ((isset($parse_url["port"])) ? ":" . $parse_url["port"] : "")
            . ((isset($parse_url["path"])) ? $parse_url["path"] : "")
            . ((isset($parse_url["query"])) ? "?" . $parse_url["query"] : "")
            . ((isset($parse_url["fragment"])) ? "#" . $parse_url["fragment"] : "")
            ;
    }

    /**
     * 打印对象并暂停
     */
    public static function dbgbp($var,$break=TRUE){
        echo '<pre>'.var_export($var,TRUE).'</pre>';
        $break==TRUE && exit();
    }

    /**
     * 函数说明: 代码访问控制
     * @author      樊振兴(nick)<nickfan81@gmail.com>
     * @history
     *              2006-08-25 樊振兴 添加了本方法
     *              2006-09-06 樊振兴 修改添加charset功能
     * @param       mixed acceptRoleLabels 允许的角色标记（包含允许角色标记的数组或'*'表示所有）
     * @param       mixed deniedRoleLabels 禁止的角色标记（包含禁止角色标记的数组或null表示无）
     * @param       string currRoleLabel 当前用户的角色标记（通常使用isset($_SESSION['user_label'])?$_SESSION['user_label']:'guest'或isset($_COOKIE['uuid'])?$_COOKIE['uuid']:'guest' 作为传入参数）
     * @param       int order 检测顺序 0 Deny,Allow 1 Allow,Deny
     * @return      bool
     */
    public static function isAccess($acceptRoleLabels = '*' , $deniedRoleLabels = null, $currRoleLabel = 'GUEST', $order=0){
        if($order==0){
            if(!empty($deniedRoleLabels)){
                if(in_array($currRoleLabel, $deniedRoleLabels)){
                    return FALSE;
                }
            }
            if($acceptRoleLabels != '*'){

                if(is_array($currRoleLabel))
                {
                    $temp = 0;
                    foreach($currRoleLabel as $val)
                    {
                        if(in_array($val, $acceptRoleLabels)){
                            $temp = $temp+1;
                        }
                    }
                    if($temp == 0)
                    {
                        return FALSE;
                    }
                }
                else
                {
                    if(!in_array($currRoleLabel, $acceptRoleLabels)){
                        return FALSE;
                    }
                }
            }
        }else{// order
            if($acceptRoleLabels != '*'){
                if(is_array($currRoleLabel))
                {
                    $temp = 0;
                    foreach($currRoleLabel as $val)
                    {
                        if(in_array($val, $acceptRoleLabels)){
                            $temp = $temp+1;
                        }
                    }
                    if($temp == 0)
                    {
                        return FALSE;
                    }
                }
                else
                {
                    if(!in_array($currRoleLabel, $acceptRoleLabels)){
                        return FALSE;
                    }
                }
            }
            if(!empty($deniedRoleLabels)){
                if(in_array($currRoleLabel, $deniedRoleLabels)){
                    return FALSE;
                }
            }
        }// order
        return TRUE;

    } // End of function isAccess

    /**
     * 函数说明: 代码访问控制
     * @author      樊振兴(nick)<nickfan81@gmail.com>
     * @history
     *              2006-08-25 樊振兴 添加了本方法
     *              2006-09-06 樊振兴 修改添加charset功能
     * @param       mixed acceptRoleLabels 允许的角色标记（包含允许角色标记的数组或'*'表示所有）
     * @param       mixed deniedRoleLabels 禁止的角色标记（包含禁止角色标记的数组或null表示无）
     * @param       string currRoleLabel 当前用户的角色标记（通常使用isset($_SESSION['user_label'])?$_SESSION['user_label']:'guest'或isset($_COOKIE['uuid'])?$_COOKIE['uuid']:'guest' 作为传入参数）
     * @param       int order 检测顺序 0 Deny,Allow 1 Allow,Deny
     * @param       string deniedInfo 被拒绝访问时返回的信息内容默认Access Denied
     * @param       string redirectUrl 被拒绝访问时转向的地址,'back'表示返回前一页；'close'表示关闭
     * @param       string frameset 被拒绝访问时转向地址所在框架,'self'表示当前框架，'page'表示整页
     * @param       string charset 系统字符编码默认utf-8
     * @return      bool
     */
    public static function isAccessAction($acceptRoleLabels = '*' , $deniedRoleLabels = null, $currRoleLabel = 'GUEST', $order=0, $deniedInfo = "Access Denied", $redirectUrl = 'back', $frameset = 'self', $charset = 'utf-8'){
        if($order==0){
            if(!empty($deniedRoleLabels)){
                if(in_array($currRoleLabel, $deniedRoleLabels)){
                    if(!headers_sent()){ header('Content-Type: text/html; charset='.$charset); }else{ echo '<meta http-equiv="Content-Type" content="text/html; charset='.$charset.'" />'; }
                    if(!empty($deniedInfo)){
                        echo "<script type=\"text/javascript\">window.alert('" . $deniedInfo . "');</script>";
                    }
                    if(!empty($redirectUrl)){
                        if($redirectUrl == 'back'){
                            echo "<script type=\"text/javascript\"> if (document.referer){ location.href=escape(document.referer);}else{history.back();}</script>";
                        }elseif($redirectUrl == 'close'){
                            echo "<script type=\"text/javascript\"> window.close();</script>";
                        }else{
                            if($frameset == 'page'){
                                echo "<script type=\"text/javascript\">if (top.location !== self.location){top.location=self.location;} location.href = '" . $redirectUrl . "';</script>";
                            }else{
                                echo "<script type=\"text/javascript\">top.window['" . $frameset . "'].location.href='" . $redirectUrl . "';</script>";
                            }
                        }
                        exit;
                        return false;
                    }
                    exit;
                    return false;
                }
            }
            if($acceptRoleLabels != '*'){
                if(!in_array($currRoleLabel, $acceptRoleLabels)){
                    if(!headers_sent()){ header('Content-Type: text/html; charset='.$charset); }else{ echo '<meta http-equiv="Content-Type" content="text/html; charset='.$charset.'" />'; }
                    if(!empty($deniedInfo)){
                        echo "<script type=\"text/javascript\">window.alert('" . $deniedInfo . "');</script>";
                    }
                    if(!empty($redirectUrl)){
                        if($redirectUrl == 'back'){
                            echo "<script type=\"text/javascript\"> if (document.referer){ location.href=escape(document.referer);}else{history.back();}</script>";
                        }elseif($redirectUrl == 'close'){
                            echo "<script type=\"text/javascript\"> window.close();</script>";
                        }else{
                            if($frameset == 'page'){
                                echo "<script type=\"text/javascript\">if (top.location !== self.location){top.location=self.location;} location.href = '" . $redirectUrl . "';</script>";
                            }else{
                                echo "<script type=\"text/javascript\">top.window['" . $frameset . "'].location.href='" . $redirectUrl . "';</script>";
                            }
                        }
                        exit;
                        return false;
                    }
                    exit;
                    return false;
                }
            }


        }else{// order

            if($acceptRoleLabels != '*'){
                if(!in_array($currRoleLabel, $acceptRoleLabels)){
                    if(!headers_sent()){ header('Content-Type: text/html; charset='.$charset); }else{ echo '<meta http-equiv="Content-Type" content="text/html; charset='.$charset.'" />'; }
                    if(!empty($deniedInfo)){
                        echo "<script type=\"text/javascript\">window.alert('" . $deniedInfo . "');</script>";
                    }
                    if(!empty($redirectUrl)){
                        if($redirectUrl == 'back'){
                            echo "<script type=\"text/javascript\"> if (document.referer){ location.href=escape(document.referer);}else{history.back();}</script>";
                        }elseif($redirectUrl == 'close'){
                            echo "<script type=\"text/javascript\"> window.close();</script>";
                        }else{
                            if($frameset == 'page'){
                                echo "<script type=\"text/javascript\">if (top.location !== self.location){top.location=self.location;} location.href = '" . $redirectUrl . "';</script>";
                            }else{
                                echo "<script type=\"text/javascript\">top.window['" . $frameset . "'].location.href='" . $redirectUrl . "';</script>";
                            }
                        }
                        exit;
                        return false;
                    }
                    exit;
                    return false;
                }
            }
            if(!empty($deniedRoleLabels)){
                if(in_array($currRoleLabel, $deniedRoleLabels)){
                    if(!headers_sent()){ header('Content-Type: text/html; charset='.$charset); }else{ echo '<meta http-equiv="Content-Type" content="text/html; charset='.$charset.'" />'; }
                    if(!empty($deniedInfo)){
                        echo "<script type=\"text/javascript\">window.alert('" . $deniedInfo . "');</script>";
                    }
                    if(!empty($redirectUrl)){
                        if($redirectUrl == 'back'){
                            echo "<script type=\"text/javascript\"> if (document.referer){ location.href=escape(document.referer);}else{history.back();}</script>";
                        }elseif($redirectUrl == 'close'){
                            echo "<script type=\"text/javascript\"> window.close();</script>";
                        }else{
                            if($frameset == 'page'){
                                echo "<script type=\"text/javascript\">if (top.location !== self.location){top.location=self.location;} location.href = '" . $redirectUrl . "';</script>";
                            }else{
                                echo "<script type=\"text/javascript\">top.window['" . $frameset . "'].location.href='" . $redirectUrl . "';</script>";
                            }
                        }
                        exit;
                        return false;
                    }
                    exit;
                    return false;
                }
            }


        }// order

    } // End of function isAccessAction

    /**
     * 函数说明: 获取分页显示代码
     *
     * @author 樊振兴(nick)<nickfan81@gmail.com>
     * @history 2006-08-25 樊振兴 添加了本方法
     * @param int num 记录数
     * @param int perpage 每页显示数量
     * @param int curr_page 当前页数
     * @param string mpurl 分页地址
     * @return string
     * @css:

    ul.multipage{
    list-style:none;
    }
    ul.multipage li{
    line-height:24px;
    display:inline;
    color:#0291d9;
    margin:0 3px;
    }

    ul.multipage li span{
    border:1px #1B9EDD solid;
    font-size:11px;
    font-weight:bold;
    color:#FFF;
    text-decoration:none;
    text-align:center;
    padding:2px 3px;
    background:#0291d9;
    }

    ul.multipage li a{
    font-size:11px;
    color:#0291d9;
    padding:2px 3px;
    border:1px #8BD1EE solid;
    text-align:center;
    text-decoration:none;
    background:#fff;
    }

    ul.multipage li a:hover{
    background:#0291d9;
    color:#fff;
    padding:2px 3px;
    border:1px #0291d9 solid;
    font-weight:bold;
    }

     */
    public static function getMultiPage($num, $perpage, $curr_page, $mpurl, $options=array()){
        $options +=array(
            'rpmnt'=>'&page=',
            'rtrm'=>'& ',
            'class'=>'bootstrap', // bootstrap, default, compat
            'textOpts'=>array(
// 			'prev'=>'prev',
// 			'next'=>'next',
// 			'first'=>'first',
// 			'last'=>'last',
// 			'current'=>'current',
// 			'total'=>'total',
                'prev'=>'&laquo;',
                'next'=>'&raquo;',
                'first'=>'|&laquo;',
                'last'=>'&raquo;|',
                'current'=>'',
                'total'=>'',
            ),
        );
        $rpmnt = $options['rpmnt'];
        $rtrm = $options['rtrm'];
        if($num > $perpage){
            $page = 10;
            $offset = 2;
            $pages = ceil($num / $perpage); //get pages
            $curr_page<1 && $curr_page=1;
            $curr_page>$pages && $curr_page=$pages;
            $from = $curr_page - $offset; //minus offset
            $to = $curr_page + $page - $offset - 1;
            if($page > $pages){
                $from = 1;
                $to = $pages;
            }else{
                if($from < 1){
                    $to = $curr_page + 1 - $from;
                    $from = 1;
                    if(($to - $from) < $page && ($to - $from) < $pages){
                        $to = $page;
                    }
                }elseif($to > $pages){
                    $from = $curr_page - $pages + $to;
                    $to = $pages;
                    if(($to - $from) < $page && ($to - $from) < $pages){
                        $from = $pages - $page + 1;
                    }
                }
            }
            $mpstr = isset($rtrm)?rtrim($mpurl,$rtrm):$mpurl;
            $fwd_back = '';
            if($options['class']=='bootstrap'){
                $fwd_back .= '<div class="pagination"><ul>';
                if($curr_page>1){
                    $fwd_back .= '<li><a href="'.$mpstr.$rpmnt.'1">'.$options['textOpts']['first'].'</a></li>';
                    $fwd_back .= '<li><a href="'.$mpstr.$rpmnt.($curr_page-1).'">'.$options['textOpts']['prev'].'</a></li>';
                }else{
                    $fwd_back .= '<li class="disabled"><a href="'.$mpstr.$rpmnt.'1">'.$options['textOpts']['first'].'</a></li>';
                    $fwd_back .= '<li class="disabled"><a href="'.$mpstr.$rpmnt.$curr_page.'">'.$options['textOpts']['prev'].'</a></li>';
                }
                for($i = $from; $i <= $to; $i++){
                    if($i != $curr_page){
                        $fwd_back .= '<li><a href="'.$mpstr.$rpmnt.$i.'">'.$i.'</a></li>';
                    }else{
                        $fwd_back .= '<li class="active"><span>'.$i.'</span></li>';
                    }
                }
                if($pages > $page){
                    if($curr_page!=$pages){
                        $fwd_back .= '<li class="disabled"><span>...</span></li>';
                        $fwd_back .= '<li><a href="'.$mpstr.$rpmnt.($curr_page+1).'">'.$options['textOpts']['next'].'</a></li>';
                        $fwd_back .= '<li><a href="'.$mpstr.$rpmnt.$pages.'">'.$options['textOpts']['last'].'</a></li>';
                    }else{
                        $fwd_back .= '<li class="disabled"><a href="'.$mpstr.$rpmnt.($curr_page).'">'.$options['textOpts']['next'].'</a></li>';
                        $fwd_back .= '<li class="disabled"><a href="'.$mpstr.$rpmnt.$pages.'">'.$options['textOpts']['last'].'</a></li>';
                    }
                }else{
                    if($curr_page!=$pages){
                        $fwd_back .= '<li><a href="'.$mpstr.$rpmnt.($curr_page+1).'">'.$options['textOpts']['next'].'</a></li>';
                        $fwd_back .= '<li><a href="'.$mpstr.$rpmnt.$pages.'">'.$options['textOpts']['last'].'</a></li>';
                    }else{
                        $fwd_back .= '<li class="disabled"><a href="'.$mpstr.$rpmnt.($curr_page).'">'.$options['textOpts']['next'].'</a></li>';
                        $fwd_back .= '<li class="disabled"><a href="'.$mpstr.$rpmnt.$pages.'">'.$options['textOpts']['last'].'</a></li>';
                    }
                }

                $fwd_back .= '<li class="disabled"><span>'.$curr_page.'/'.$pages.' ('.$num.')</span></li>';
                $fwd_back .= '</ul></div>';
            }elseif($options['class']=='compat'){
                $fwd_back .= '<div class="showpage"><p>';
                if($curr_page>1){
                    $fwd_back .= '<a href="'.$mpstr.$rpmnt.($curr_page-1).'">'.$options['textOpts']['prev'].'</a> ';
                }else{
                    $fwd_back .= '<a href="'.$mpstr.$rpmnt.(1).'">'.$options['textOpts']['first'].'</a> ';
                }
                if($pages > $page){
                    if($curr_page!=$pages){
                        $fwd_back .= '<a href="'.$mpstr.$rpmnt.($curr_page+1).'">'.$options['textOpts']['next'].'</a> ';
                    }else{
                        $fwd_back .= '<a href="'.$mpstr.$rpmnt.$pages.'">'.$options['textOpts']['last'].'</a> ';
                    }
                }else{
                    if($curr_page!=$pages){
                        $fwd_back .= '<a href="'.$mpstr.$rpmnt.($curr_page+1).'">'.$options['textOpts']['next'].'</a> ';
                    }else{
                        $fwd_back .= '<a href="'.$mpstr.$rpmnt.$pages.'">'.$options['textOpts']['last'].'</a> ';
                    }
                }
                $fwd_back .= '</p></div>';
            }else{
                $fwd_back .= '<ul class="multipage">';
                if($curr_page>1){
                    $fwd_back .= '<li class="multipage_first"><a href="'.$mpstr.$rpmnt.'1">'.$options['textOpts']['first'].'</a></li>';
                    $fwd_back .= '<li class="multipage_prev"><a href="'.$mpstr.$rpmnt.($curr_page-1).'">'.$options['textOpts']['prev'].'</a></li>';
                }
                for($i = $from; $i <= $to; $i++){
                    if($i != $curr_page){
                        $fwd_back .= '<li class="multipage_num"><a href="'.$mpstr.$rpmnt.$i.'">'.$i.'</a></li>';
                    }else{
                        $fwd_back .= '<li class="multipage_cur"><span>'.$i.'</span></li>';
                    }
                }
                if($pages > $page){
                    if($curr_page!=$pages){
                        $fwd_back .= '<li class="multipage_ellip">...</li>';
                        $fwd_back .= '<li class="multipage_next"><a href="'.$mpstr.$rpmnt.($curr_page+1).'">'.$options['textOpts']['next'].'</a></li>';
                        $fwd_back .= '<li class="multipage_last"><a href="'.$mpstr.$rpmnt.$pages.'">'.$pages.''.$options['textOpts']['last'].'</a></li>';
                    }
                }else{
                    if($curr_page!=$pages){
                        $fwd_back .= '<li class="multipage_next"><a href="'.$mpstr.$rpmnt.($curr_page+1).'">'.$options['textOpts']['next'].'</a></li>';
                        $fwd_back .= '<li class="multipage_last"><a href="'.$mpstr.$rpmnt.$pages.'">'.$options['textOpts']['last'].'</a></li>';
                    }
                }
                $fwd_back .= '<li class="multipage_stats"><span>'.$curr_page.'/'.$pages.' ('.$num.')</span></li>';
                $fwd_back .= '</ul>';
            }
            $multipage = $fwd_back;
            return $multipage;
        }
    }// end of function getMultiPage


    /**
     * page redirect
     *
     * @name redirect
     * @author nickfan<nickfan81@gmail.com>
     * @last nickfan<nickfan81@gmail.com>
     * @update 2006/01/06 13:41:47
     * @version 0.1
     * @method Multi Params:
     * @param string $url (default blank page)
     * @param string $method header/refresh/location/page (default=refresh)
     * @param string $frame blank/top/self/parent/[userdefine] (default=self)
     */
    public static function respRedirect(){
        $thisargs = func_get_args();
        $thisurl = isset($thisargs[0])?$thisargs[0]:(isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:"about:blank");
        $thismethod = isset($thisargs[1])?$thisargs[1]:'refresh';
        $thistime = isset($thisargs[2])?intval($thisargs[2]):0;
        $thisframe = isset($thisargs[3]) && is_string($thisargs[3]) && ($thisargs[3] != '' || !empty($thisargs[3]))?$thisargs[3]:'self';
        $thistarget = in_array($thisframe, array('blank', 'top', 'self', 'parent'))?"_" . $thisframe:$thisframe;
        switch($thismethod){
            case 'header':
                header("Location:" . $thisurl);
                break;
            case 'refresh':
                echo "<meta http-equiv=\"Window-target\" content=\"" . $thistarget . "\"><meta http-equiv=\"Refresh\" content=\"" . $thistime . "; url=" . $thisurl . "\">";
                break;
            case 'location':
                echo "<script type=\"text/javascript\">top.window['" . $thisframe . "'].location.href='" . $thisurl . "';</script>";
                break;
            case 'page':
                echo "<script type=\"text/javascript\">if (top.location !== self.location){top.location=self.location;} location.href = '" . $thisurl . "';</script>";
                break;
            default:
                echo "<meta http-equiv=\"Window-target\" content=\"" . $thistarget . "\"><meta http-equiv=\"Refresh\" content=\"" . $thistime . "; url=" . $thisurl . "\">";
        }
    }

    /**
     * 函数说明: 截取字符串
     *
     * @author 樊振兴(nick)<nickfan81@gmail.com>
     * @history 2006-08-25 樊振兴 添加了本方法
     * @param string title 字符串
     * @param int length 截取长度
     * @param string etc 截取后附加字符
     * @param string enc 字符编码
     * @return string
     */
    public static function subString($str = '', $start = 0, $length = 0, $enc = 'utf-8', $etc = '...'){
        if ($length == 0)
            return '';
        $enc = strtolower($enc);
        $enc_length = $enc == 'utf-8'?3:2;

        if(extension_loaded('mbstring')){
            $newstr = mb_substr($str, $start, $length, $enc);
            $strlen = mb_strlen($str, $enc);
            $newstrlen = mb_strlen($newstr, $enc);
        }elseif($enc == 'utf-8'){
            $strlen = strlen($str);

            $r = array();
            $n = 0;
            $m = 0;
            for($i = 0; $i < $strlen; $i++){
                $x = substr($str, $i, 1);
                $a = base_convert(ord($x), 10, 2);
                $a = substr('00000000' . $a, -8);
                if ($n < $start){
                    if (substr($a, 0, 1) == 0){
                    }elseif (substr($a, 0, 3) == 110){
                        $i += 1;
                    }elseif (substr($a, 0, 4) == 1110){
                        $i += 2;
                    }
                    $n++;
                }else{
                    if (substr($a, 0, 1) == 0){
                        $r[] = substr($str, $i, 1);
                    }elseif (substr($a, 0, 3) == 110){
                        $r[] = substr($str, $i, 2);
                        $i += 1;
                    }elseif (substr($a, 0, 4) == 1110){
                        $r[] = substr($str, $i, 3);
                        $i += 2;
                    }else{
                        $r[] = '';
                    }
                    if (++$m >= $length){
                        break;
                    }
                }
            }
            $newstr = implode('', $r);
            $newstrlen = strlen($newstr);
        }else{
            $string = "";
            $count = 1;
            $strlen = strlen($str);
            for ($i = 0; $i < $strlen; $i++){
                if (($count + 1 - $start) > $length){
                    // $str  .= "...";
                    break;
                }elseif ((ord(substr($str, $i, 1)) <= 128) && ($count < $start)){
                    $count++;
                }elseif ((ord(substr($str, $i, 1)) > 128) && ($count < $start)){
                    $count = $count + 2;
                    $i = $i + $enc_length-1;
                }elseif ((ord(substr($str, $i, 1)) <= 128) && ($count >= $start)){
                    $string .= substr($str, $i, 1);
                    $count++;
                }elseif ((ord(substr($str, $i, 1)) > 128) && ($count >= $start)){
                    $string .= substr($str, $i, $enc_length);
                    $count = $count + 2;
                    $i = $i + $enc_length-1;
                }
            }
            $newstr = $string;
            $newstrlen = strlen($newstr);
        }
        return ($strlen > $newstrlen) ? $newstr . $etc : $str;
    }

    public static function base32_encode($input) {
        // Reference: http://www.ietf.org/rfc/rfc3548.txt
        // If you want build alphabet own, you should modify the decode section too.
        $BASE32_ALPHABET = 'abcdefghijklmnopqrstuvwxyz234567';
        $output = '';
        $v = 0;
        $vbits = 0;

        for ($i = 0, $j = strlen($input); $i < $j; $i++) {
            $v <<= 8;
            $v += ord($input[$i]);
            $vbits += 8;

            while ($vbits >= 5) {
                $vbits -= 5;
                $output .= $BASE32_ALPHABET[$v >> $vbits];
                $v &= ((1 << $vbits) - 1);
            }
        }

        if ($vbits > 0) {
            $v <<= (5-$vbits);
            $output .= $BASE32_ALPHABET[$v];
        }

        return $output;
    }

    public static function base32_decode($input) {
        $output = '';
        $v = 0;
        $vbits = 0;

        for($i = 0, $j = strlen($input); $i < $j; $i++) {
            $v <<= 5;
            if ($input[$i] >= 'a' && $input[$i] <= 'z') {
                $v += (ord($input[$i]) - 97);
            }
            elseif ($input[$i] >= '2' && $input[$i] <= '7') {
                $v += (24 + $input[$i]);
            }
            else {
                exit(1);
            }

            $vbits += 5;
            while($vbits >= 8){
                $vbits -= 8;
                $output .= chr($v >> $vbits);
                $v &= ((1 << $vbits) - 1);
            }
        }
        return $output;
    }
    /*
     * 64们机和32位机兼容的ip2long
     */
    public static function myip2long($strIP)
    {
        $longIP=ip2long($strIP);
        if ($longIP < 0){
            $longIP += 4294967296;
        }
        return $longIP;
    }

    // decbin32
    // In order to simplify working with IP addresses (in binary) and their
    // netmasks, it is easier to ensure that the binary strings are padded
    // with zeros out to 32 characters - IP addresses are 32 bit numbers
    public static function decbin32($dec) {
        return str_pad(decbin($dec), 32, '0', STR_PAD_LEFT);
    }

    // ip_in_range
    // This function takes 2 arguments, an IP address and a "range" in several
    // different formats.
    // Network ranges can be specified as:
    // 1. Wildcard format:     1.2.3.*
    // 2. CIDR format:         1.2.3/24  OR  1.2.3.4/255.255.255.0
    // 3. Start-End IP format: 1.2.3.0-1.2.3.255
    // The function will return true if the supplied IP is within the range.
    // Note little validation is done on the range inputs - it expects you to
    // use one of the above 3 formats.
    public static function ip_in_range($ip, $range) {
        if (strpos($range, '/') !== false) {
            // $range is in IP/NETMASK format
            list($range, $netmask) = explode('/', $range, 2);
            if (strpos($netmask, '.') !== false) {
                // $netmask is a 255.255.0.0 format
                $netmask = str_replace('*', '0', $netmask);
                $netmask_dec = ip2long($netmask);
                return ((ip2long($ip) & $netmask_dec) == (ip2long($range) & $netmask_dec));
            } else {
                // $netmask is a CIDR size block
                // fix the range argument
                $x = explode('.', $range);
                while (count($x) < 4)
                    $x[] = '0';
                list($a, $b, $c, $d) = $x;
                $range = sprintf("%u.%u.%u.%u", empty($a) ? '0' : $a, empty($b) ? '0' : $b, empty($c) ? '0' : $c, empty($d) ? '0' : $d);
                $range_dec = ip2long($range);
                $ip_dec = ip2long($ip);

                # Strategy 1 - Create the netmask with 'netmask' 1s and then fill it to 32 with 0s
                #$netmask_dec = bindec(str_pad('', $netmask, '1') . str_pad('', 32-$netmask, '0'));

                # Strategy 2 - Use math to create it
                $wildcard_dec = pow(2, (32 - $netmask)) - 1;
                $netmask_dec = ~$wildcard_dec;

                return (($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec));
            }
        } else {
            // range might be 255.255.*.* or 1.2.3.0-1.2.3.255
            if (strpos($range, '*') !== false) { // a.b.*.* format
                // Just convert to A-B format by setting * to 0 for A and 255 for B
                $lower = str_replace('*', '0', $range);
                $upper = str_replace('*', '255', $range);
                $range = "$lower-$upper";
            }

            if (strpos($range, '-') !== false) { // A-B format
                list($lower, $upper) = explode('-', $range, 2);
                $lower_dec = (float) sprintf("%u", ip2long($lower));
                $upper_dec = (float) sprintf("%u", ip2long($upper));
                $ip_dec = (float) sprintf("%u", ip2long($ip));
                return (($ip_dec >= $lower_dec) && ($ip_dec <= $upper_dec));
            }
            //echo 'Range argument is not in 1.2.3.4/24 or 1.2.3.4/255.255.255.0 format';
            return false;
        }
    }

    /**
     * 根据键值求二维数组的交集
     *
     * @param <Array> $array1
     * @param <Array> $array2
     *
     * @return Array
     */
    function array_common($array1,$array2,$compare_string = 'id')
    {
        if (!is_array($array1) || !is_array($array2))
        {
            return false;
        }
        $compare_arr = array();
        foreach($array1 as $value)
        {
            $compare_arr[] = $value[$compare_string];
        }

        $arr_result = array();
        foreach ($array2 as $value)
        {
            if(in_array($value[$compare_string],$compare_arr))
            {
                $arr_result[] = $value;
            }
        }
        return $arr_result;
    }

    /**
     * 二维数组相差，根据ID
     * @param <Array> $source_array
     * @param <Array> $target_array
     *
     * @return Array
     */
    public static function my_array_diff($source_array = array(),$target_array = array())
    {
        if(count($target_array))
        {
            $id_arr = array();
            $result_arr = array();

            foreach($target_array as $key=>$value)
            {
                $id_arr[] = $value['id'];
            }

            foreach($source_array as $key=>$value)
            {
                if(!in_array($value['id'],$id_arr))
                {
                    $result_arr[] = $value;
                }
            }
            return $result_arr;
        }
        else
        {
            return $source_array;
        }
    }

    /**
     * 简化输出数组
     * @param $item
     * @param $key
     * @param $requestkeys
     * @example @array_walk($result_arr,'util::simplify_return_array',$requestkeys);
     */
    public static function simplify_return_array(&$item,$key,$requestkeys){
        $diffkeys = array_diff(array_keys($item),$requestkeys);
        foreach ($diffkeys as $diffkey){
            unset($item[$diffkey]);
        }
    }

    /**
     * 过滤数组，保留指定键值列表
     * @param array $array
     * @param array $requestkeys
     * @example util::filter_keys（$array,$requestkeys);
     */
    public static function filter_keys(&$array,$requestkeys){
        foreach ($array as $key=>$val){
            if(!in_array($key,$requestkeys)){
                unset($array[$key]);
            }
        }
    }

    /**
     * 返回客户端IP地址
     *
     * @return string
     */
    public static function getClientIP() {
        global $_SERVER;
        if (getenv ( 'HTTP_CLIENT_IP' ) && strcasecmp ( getenv ( 'HTTP_CLIENT_IP' ), 'unknown' )) {
            $onlineip = getenv ( 'HTTP_CLIENT_IP' );
        } elseif (getenv ( 'HTTP_X_FORWARDED_FOR' ) && strcasecmp ( getenv ( 'HTTP_X_FORWARDED_FOR' ), 'unknown' )) {
            $onlineip = getenv ( 'HTTP_X_FORWARDED_FOR' );
        } elseif (getenv ( 'REMOTE_ADDR' ) && strcasecmp ( getenv ( 'REMOTE_ADDR' ), 'unknown' )) {
            $onlineip = getenv ( 'REMOTE_ADDR' );
        } elseif (getenv ( 'HTTP_X_REAL_IP' ) && strcasecmp ( getenv ( 'HTTP_X_REAL_IP' ), 'unknown' )) {
            $onlineip = getenv ( 'HTTP_X_REAL_IP' );
        } elseif (isset ( $_SERVER ['REMOTE_ADDR'] ) && $_SERVER ['REMOTE_ADDR'] && strcasecmp ( $_SERVER ['REMOTE_ADDR'], 'unknown' )) {
            $onlineip = $_SERVER ['REMOTE_ADDR'];
        }
        $onlineip = preg_replace ( "/^([\d\.]+).*/", "\\1", $onlineip );
        preg_match ( "/[\d\.]{7,15}/", $onlineip, $match );
        $onlineip = $match [0] ? $match [0] : '0.0.0.0';
        return $onlineip;
    }

    /**
     * 判断是否为ajax请求
     */
    public static function isAjaxRequest(){
        global $_SERVER;
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    /**
     * 返回一个GUID
     *
     * @return string
     */
    public static function reGUID() {
        list ( $usec, $sec ) = explode ( " ", microtime () );
        $curtm = $sec . substr ( $usec, 2, 3 );
        $svname = isset ( $_ENV ['COMPUTERNAME'] ) ? $_ENV ['COMPUTERNAME'] : 'localhost';
        $svip = isset ( $_SERVER ['SERVER_ADDR'] ) ? $_SERVER ['SERVER_ADDR'] : '127.0.0.1';
        $tmp = rand ( 0, 1 ) ? '-' : '';
        $randstr = $tmp . rand ( 1000, 9999 ) . rand ( 1000, 9999 ) . rand ( 1000, 9999 ) . rand ( 100, 999 ) . rand ( 100, 999 );
        $cstr = $svname . '/' . $svip . ':' . $curtm . ':' . $randstr;
        $md5cstr = strtolower ( md5 ( $cstr ) );
        return substr ( $md5cstr, 0, 8 ) . '-' . substr ( $md5cstr, 8, 4 ) . '-' . substr ( $md5cstr, 12, 4 ) . '-' . substr ( $md5cstr, 16, 4 ) . '-' . substr ( $md5cstr, 20 );
    }


    /**
     * 函数说明: 构建查询字符串
     * @author      樊振兴(nick)<nickfan81@gmail.com>
     * @history
     *              2007-12-03 樊振兴 添加了本方法
     * @param       array $reqstruct 请求数组
     * @param       array/null $upstruct 更新用的数组
     * @return      string
     */
    public static function buildQstr($reqstruct,$upstruct=NULL) {
        $qstruct=$reqstruct;
        if(!empty($upstruct)){
            $qstruct=array_merge($qstruct,$upstruct);
        }
        $qstr='';
        foreach($qstruct as $qkey=>$qrow){
            if(is_array($qrow)){
                foreach($qrow as $qrval){
                    $qstr.='&'.$qkey.'[]='.urlencode($qrval);
                }
            }else{
                $qstr.='&'.$qkey.'='.urlencode($qrow);
            }
        }
        $qstr=ltrim($qstr,'&');
        return $qstr;
    }


    /**
     * 函数说明: 构建排序链接
     * @author      樊振兴(nick)<nickfan81@gmail.com>
     * @history
     *              2007-12-03 樊振兴 添加了本方法
     * @param       string $urlBase 请求的基本url
     * @param       array $reqstruct 请求数组
     * @param       array/null $setstruct 更新用的数组
     * array(
     * 	'name'=>'ID'		// 设定排序的字段显示名
     * 	'key'=>'id',		// 设定排序的字段
     * 	//'sort'=>'DESC',	//（可选）默认排序  'ASC' 'DESC'/'ASC'
     * 	//'keep'=>FALSE,	// (可选) 保持原有排序数组  FALSE TRUE/FALSE
     * 	//'css'=>'listOrder'	//（可选）默认链接样式  'listOrder'
     * 	//'cssActive'=>'listOrderActive'	//（可选）默认链接样式  'list-order'
     * ),
     * @return      string
     */
    public static function buildOrderLink($urlBase,$requestStruct,$setStruct) {
        // 返回的Content字符串
        $returnContentStr = '';
        // 返回的排序值字符串;
        $returnSortStr = '';

        // 请求设定的排序关键字段显示名称
        $reqOrderName = isset($setStruct['name'])?$setStruct['name']:'ID';
        // 请求设定的排序关键字段
        $reqOrderKey = isset($setStruct['key'])?$setStruct['key']:'id';
        // 当前请求中是否包含了排序请求
        $containReqOrder = FALSE;
        // 当前请求中是否包含了请求排序关键字段
        $containReqOrderKey = FALSE;
        // 当前请求中包含了请求排序关键字段时的排序状态
        $containReqOrderSort = NULL;
        // 当前请求中包含了请求排序关键字段时的排序数组序号
        $containReqOrderLine = 0;
        // 默认排序值
        $orderDefaultSort = isset($setStruct['sort'])?$setStruct['sort']:'ASC';
        // 是否保持原有排序数组
        $keepOrderSort = isset($setStruct['keep']) && $setStruct['keep']==TRUE ?TRUE:FALSE;
        // 默认链接样式
        $linkCss = isset($setStruct['css'])?$setStruct['css']:'listOrder';
        // 默认激活链接样式
        $linkCssActive = isset($setStruct['cssActive'])?$setStruct['cssActive']:'listOrder Active';

        $sortStrAssoc = array(
            'DESC'=>'&#9660;',
            'ASC'=>'&#9650;',
        );

        // 包含了排序请求
        if(!empty($requestStruct['order']) && !empty($requestStruct['sort'])){
            $containReqOrder = TRUE;
            // 查找是否包含当前请求字段
            foreach($requestStruct['order'] as $line=>$order){
                if(isset($requestStruct['sort'][$line]) && $order==$reqOrderKey){
                    $containReqOrderKey = TRUE;
                    $containReqOrderLine = $line;
                    $sort = strtoupper($requestStruct['sort'][$line]);
                    !in_array($sort,array('ASC','DESC','RAND','RANDOM','NULL')) && $sort= 'ASC';
                    // 输出当前排序状态
                    $containReqOrderSort = $sort;
                    break;
                }
            }
        }
        $requestStructBuild = $requestStruct;
        unset($requestStructBuild['page']);
        // 如果请求中包含了排序请求数组
        if($containReqOrder==TRUE){
            // 如果请求中包含了排序请求字段
            if($containReqOrderKey==TRUE){
                // 如果需要保持现有排序数组
                if($keepOrderSort==TRUE){
                    // 将排序请求反向
                    $setSort = $containReqOrderSort=='ASC'?'DESC':'ASC';
                    $requestStructBuild['sort'][$containReqOrderLine] = $setSort;
                }else{
                    // 如果不需要保持现有排序数组
                    $requestStructBuild['order']=array(
                        $reqOrderKey,
                    );
                    // 将排序请求反向
                    $setSort = $containReqOrderSort=='ASC'?'DESC':'ASC';
                    $requestStructBuild['sort']=array(
                        $setSort,
                    );
                }
            }else{
                // 如果请求中不包含排序请求字段

                // 如果需要保持现有排序数组
                if($keepOrderSort==TRUE){
                    // 直接添加排序请求
                    $requestStructBuild['order'][] = $reqOrderKey;
                    $requestStructBuild['sort'][]  = $orderDefaultSort;
                }else{
                    // 如果不需要保持现有排序数组
                    $requestStructBuild['order']=array(
                        $reqOrderKey,
                    );
                    // 将排序请求设定为默认排序值
                    $setSort = $orderDefaultSort;
                    $requestStructBuild['sort']=array(
                        $setSort,
                    );
                }
            }
        }else{
            $requestStructBuild['order']=array(
                $reqOrderKey,
            );
            $requestStructBuild['sort']=array(
                $orderDefaultSort,
            );
        }
        $queryStringBuild = util::buildQstr($requestStructBuild);
        $returnUrl = $urlBase.$queryStringBuild;
        if(!empty($containReqOrderSort)){
            $returnSortStr = $sortStrAssoc[$containReqOrderSort];
            $returnContentStr = '<a href="'.$returnUrl.'" '.(empty($linkCssActive)?'':'class="'.$linkCssActive.'"').' >'.$reqOrderName.' '.$returnSortStr.'</a>';
        }else{
            $returnContentStr = '<a href="'.$returnUrl.'" '.(empty($linkCss)?'':'class="'.$linkCss.'"').' >'.$reqOrderName.' '.$returnSortStr.'</a>';
        }
        return $returnContentStr;
    }

    /**
     * 函数说明: 判断是否已发送指定名称域的文件
     * @author      樊振兴(nick)<nickfan81@gmail.com>
     * @history
     *              2006-08-25 樊振兴 添加了本方法
     * @param       string item 文件域的名称(id/name)
     * @return      bool
     */
    public static function issetFile($item){
        if(isset($_FILES[$item]) && !empty($_FILES[$item]['name'])){
            if(!is_array($_FILES[$item]['name'])){
                return true;
            }else{
                $isset = false;
                for($i = 0;$i < count($_FILES[$item]['name']);$i++){
                    if(!empty($_FILES[$item]['name'][$i])){
                        $isset = true;
                    }
                }
                return $isset;
            }
        }else{
            return false;
        }
    }

    /**
     * 函数说明: 返回一个随机数
     *
     * @author 樊振兴(nick)<nickfan81@gmail.com>
     * @history 2006-08-25 樊振兴 添加了本方法
     * @return int
     */
    public static function reRandNum(){
        static $authnum;
        srand((double)microtime() * 1000000);
        while(($authnum = mt_rand() % 10000) < 1000);
        return $authnum;
    }

    /**
     * 函数说明: 返回一个随机hash字符串(长度32)
     *
     * @author 樊振兴(nick)<nickfan81@gmail.com>
     * @history 2006-08-25 樊振兴 添加了本方法
     * @return string
     */
    public static function reRandToken(){
        srand((double)microtime() * 1000000);
        return md5(uniqid(mt_rand(), true));
    }

    /**
     * 函数说明: 返回一个指定长度的随机字符串
     *
     * @author 樊振兴(nick)<nickfan81@gmail.com>
     * @history 2006-08-25 樊振兴 添加了本方法
     * @param int len 字符串长度
     * @return string
     */
    public static function reRandStr($len = 3){
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        mt_srand((double)microtime() * 1000000 * getmypid());
        $outstr = "";
        while(strlen($outstr) < $len)
            $outstr .= substr($chars, (mt_rand() % strlen($chars)), 1);
        return $outstr;
    } // end of function reRandStr

    /**
     * 获取slug化的字符串
     */
    public static function reSlug($str){
        $str = strtolower($str);
        $str = preg_replace('/[^a-z0-9_ -]+/', '', $str);
        $str = str_replace(' ', '-', $str);
        return trim($str, '-');
    }

    /**
     *
     * @name hex2Bin
     * @author author<author@example.com>
     * @last author<author@example.com>
     * @update 2006/01/06 13:41:47
     * @version 0.1
     * @param data $data
     * @return data $newdata
     */
    public static function hex2bin($str){
        $sbin = "";
        $len = strlen( $str );
        for ( $i = 0; $i < $len; $i += 2 ) {
            $sbin .= pack( "H*", substr( $str, $i, 2 ) );
        }

        return $sbin;
    }

    /**
     * 替换%u到\u
     * @param string $str
     * @return string
     */
    public static function cvEscape($str=''){
        return preg_replace("/(%u)(\w{4})/i","\\u$2", $str);
    }

    /**
     * 替换\u到%u
     * @param string $str
     * @return string
     */
    public static function uncvEscape($str=''){
        return preg_replace('/\\\(u)(\w{4})/i',"%u$2", $str);
    }

    /**
     * js 转换escape编码
     * @param string $str
     * @param string $charset
     */
    public static function jsEscape($str, $charset = 'utf-8') {
        if ($charset == 'utf-8') {
            preg_match_all ( "/[\xc2-\xdf][\x80-\xbf]+|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}|[\x01-\x7f]+/e", $str, $r );
            $str = $r [0];
            $l = count ( $str );
            for($i = 0; $i < $l; $i ++) {
                $value = ord ( $str [$i] [0] );
                if ($value < 223) {
                    $str [$i] = rawurlencode ( utf8_decode ( $str [$i] ) );
                } else {
                    $str [$i] = "%u" . strtoupper ( bin2hex ( iconv ( "UTF-8", "UCS-2", $str [$i] ) ) );
                }
            }
        } else {
            preg_match_all ( "/[\x80-\xff].|[\x01-\x7f]+/", $str, $r );
            $str = $r [0];
            foreach ( $r as $k => $v ) {
                if (ord ( $v [0] ) < 128)
                    $str [$k] = rawurlencode ( $v );
                else
                    $str [$k] = "%u" . bin2hex ( iconv ( "GB2312", "UCS-2", $v ) );
            }
        }
        return join ( "", $str );
    }

    /**
     * js escape php 实现
     *
     * @param $string the
     *        	sting want to be escaped
     * @param
     *        	$in_encoding
     * @param
     *        	$out_encoding
     */
    public static function jsEscapeEx($string, $in_encoding = 'UTF-8', $out_encoding = 'UCS-2') {
        $return = '';
        if (function_exists ( 'mb_get_info' )) {
            for($x = 0; $x < mb_strlen ( $string, $in_encoding ); $x ++) {
                $str = mb_substr ( $string, $x, 1, $in_encoding );
                if (strlen ( $str ) > 1) { // 多字节字符
                    $return .= '%u' . strtoupper ( bin2hex ( mb_convert_encoding ( $str, $out_encoding, $in_encoding ) ) );
                } else {
                    $return .= '%' . strtoupper ( bin2hex ( $str ) );
                }
            }
        }
        return $return;
    }

    /**
     * js 转换escape 解码
     * @param string $str
     * @param string $charset
     */
    public static function jsUnEscape($str, $charset = 'utf-8') {
        $str = rawurldecode ( $str );
        preg_match_all ( "/(?:%u.{4})|.+/", $str, $r );
        $ar = $r [0];
        foreach ( $ar as $k => $v ) {
            if (substr ( $v, 0, 2 ) == "%u" && strlen ( $v ) == 6)
                $ar [$k] = iconv ( "UCS-2", $charset, pack ( "H4", substr ( $v, - 4 ) ) );
        }
        return join ( "", $ar );
    }


    public static function r4crypt ($pwd, $data, $case='en') {

        if ($case == 'de')
        {

            $data = base64_decode($data);

        }

        $key[] = "";
        $box[] = "";
        $temp_swap = "";
        $pwd_length = 0;

        $pwd_length = strlen($pwd);

        for ($i = 0; $i < 255; $i++) {

            $key[$i] = ord(substr($pwd, ($i % $pwd_length)+1, 1));
            $box[$i] = $i;

        }

        $x = 0;

        for ($i = 0; $i < 255; $i++) {

            $x = ($x + $box[$i] + $key[$i]) % 256;
            $temp_swap = $box[$i];

            $box[$i] = $box[$x];
            $box[$x] = $temp_swap;

        }

        $temp = "";
        $k = "";

        $cipherby = "";
        $cipher = "";

        $a = 0;
        $j = 0;

        for ($i = 0; $i < strlen($data); $i++) {

            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;

            $temp = $box[$a];
            $box[$a] = $box[$j];

            $box[$j] = $temp;

            $k = $box[(($box[$a] + $box[$j]) % 256)];
            $cipherby = ord(substr($data, $i, 1)) ^ $k;

            $cipher .= chr($cipherby);

        }

        if ($case == 'de')
        {

            $cipher = base64_decode(base64_encode($cipher));

        }
        else
        {

            $cipher = base64_encode($cipher);

        }

        return $cipher;

    }

    /**
     * 把SimpleXml对象转换为array
     */
    public static function convertSimpleXmlElementObject2Array($simpleXmlElementObject, &$recursionDepth=0) {

        if ($recursionDepth == 0) {
            if (gettype($simpleXmlElementObject)=='object' && get_class($simpleXmlElementObject) != 'SimpleXMLElement') {
                // If the external caller doesn't call this function initially
                // with a SimpleXMLElement object, return now.
                return(null);
            } else {
                // Store the original SimpleXmlElementObject sent by the caller.
                // We will need it at the very end when we return from here for good.
                $callerProvidedSimpleXmlElementObject = $simpleXmlElementObject;
            }
        } // End of if ($recursionDepth == 0) {

        if (gettype($simpleXmlElementObject)=='object' && get_class($simpleXmlElementObject) == 'SimpleXMLElement') {
            // Get a copy of the simpleXmlElementObject
            $copyOfsimpleXmlElementObject = $simpleXmlElementObject;
            // Get the object variables in the SimpleXmlElement object for us to iterate.
            $simpleXmlElementObject = get_object_vars($simpleXmlElementObject);
        }

        // It needs to be an array of object variables.
        if (is_array($simpleXmlElementObject)) {
            // Initialize the result array.
            $resultArray = array();
            // Is the input array size 0? Then, we reached the rare CDATA text if any.
            if (count($simpleXmlElementObject) <= 0) {
                // Let us return the lonely CDATA. It could even be whitespaces.
                return (trim(strval($copyOfsimpleXmlElementObject)));
            }

            // Let us walk through the child elements now.
            foreach($simpleXmlElementObject as $key=>$value) {
                // When this block of code is commented, XML attributes will be
                // added to the result array.
                // Uncomment the following block of code if XML attributes are
                // NOT required to be returned as part of the result array.
                /*
                if((is_string($key)) && ($key == SIMPLE_XML_ELEMENT_OBJECT_PROPERTY_FOR_ATTRIBUTES)) {
                    continue;
                }
                */
                // Let us recursively process the current element we just visited.
                // Increase the recursion depth by one.
                $recursionDepth++;
                $resultArray[$key] = util::convertSimpleXmlElementObject2Array($value, $recursionDepth);
                // Decrease the recursion depth by one.
                $recursionDepth--;
            } // End of foreach($simpleXmlElementObject as $key=>$value) {

            if ($recursionDepth == 0) {
                // That is it. We are heading to the exit now.
                // Set the XML root element name as the root [top-level] key of
                // the associative array that we are going to return to the caller of this
                // recursive function.
                $tempArray = $resultArray;
                $resultArray = array();
                $resultArray[$callerProvidedSimpleXmlElementObject->getName()] = $tempArray;
            }

            return ($resultArray);
        } else {
            // We are now looking at either the XML attribute text or
            // the text between the XML tags.
            return (trim(strval($simpleXmlElementObject)));
        } // End of else
    } // End of function convertSimpleXmlElementObjectIntoArray.

    public static function dispUPCAbarcode($code)
    {
        $lw = 2; $hi = 100;
        $Lencode = array('0001101','0011001','0010011','0111101','0100011',
            '0110001','0101111','0111011','0110111','0001011');
        $Rencode = array('1110010','1100110','1101100','1000010','1011100',
            '1001110','1010000','1000100','1001000','1110100');
        $ends = '101'; $center = '01010';

        /* UPC-A Must be 11 digits, we compute the checksum. */
        if ( strlen($code) != 11 ) { throw new MyRuntimeException('UPC-A Must be 11 digits.',500); }

        /* Compute the EAN-13 Checksum digit */
        $ncode = '0'.$code;
        $even = 0; $odd = 0;
        for ($x=0;$x<12;$x++)
        {
            if ($x % 2) { $odd += $ncode[$x]; } else { $even += $ncode[$x]; }
        }

        $code.=(10 - (($odd * 3 + $even) % 10)) % 10;

        /* Create the bar encoding using a binary string */
        $bars=$ends;
        $bars.=$Lencode[$code[0]];
        for($x=1;$x<6;$x++)
        {
            $bars.=$Lencode[$code[$x]];
        }

        $bars.=$center;

        for($x=6;$x<12;$x++)
        {
            $bars.=$Rencode[$code[$x]];
        }

        $bars.=$ends;

        /* Generate the Barcode Image */
        $img = ImageCreate($lw*95+30,$hi+30);
        $fg = ImageColorAllocate($img, 0, 0, 0);
        $bg = ImageColorAllocate($img, 255, 255, 255);
        ImageFilledRectangle($img, 0, 0, $lw*95+30, $hi+30, $bg);

        $shift=10;

        for ($x=0;$x<strlen($bars);$x++)
        {
            if (($x<10) || ($x>=45 && $x<50) || ($x >=85)) { $sh=10; } else { $sh=0; }
            if ($bars[$x] == '1') { $color = $fg; } else { $color = $bg; }
            ImageFilledRectangle($img, ($x*$lw)+15,5,($x+1)*$lw+14,$hi+5+$sh,$color);
        }

        /* Add the Human Readable Label */
        ImageString($img,4,5,$hi-5,$code[0],$fg);

        for ($x=0;$x<5;$x++)
        {
            ImageString($img,5,$lw*(13+$x*6)+15,$hi+5,$code[$x+1],$fg);
            ImageString($img,5,$lw*(53+$x*6)+15,$hi+5,$code[$x+6],$fg);
        }

        ImageString($img,4,$lw*95+17,$hi-5,$code[11],$fg);

        /* Output the Header and Content. */
        header("Content-Type: image/png");
        ImagePNG($img);
    }

    /**
     * 获取两个时间戳的月数差(月时间格式化后数字时间差)
     * 例如2012-05-01 17:40:01 与 2012-06-03 12:00:00 相差的月字面数字（不考虑时间部分，为1月）
     * @param int $tsa
     * @param int $tsb
     */
    public static function getMonsNumDiff($tsa=0,$tsb=0){
        empty($tsa) && $tsa=time();
        empty($tsb) && $tsb=time();
        $tsaDateArr = getdate($tsa);
        $tsbDateArr = getdate($tsb);
        $startMonth = $tsaDateArr['mon'];
        $startYear = $tsaDateArr['year'];
        $endMonth = $tsbDateArr['mon'];
        $endYear = $tsbDateArr['year'];
        return abs(($endYear - $startYear) * 12 + ($endMonth - $startMonth));
    }

    /**
     * 获取两个时间戳的日期数差(日期时间格式化后数字时间差)
     * 例如2012-05-01 17:40:01 与 2012-05-03 12:00:00 相差的日期字面数字（不考虑时间部分，为2天）
     * @param int $tsa
     * @param int $tsb
     */
    public static function getDayNumDiff($tsa=0,$tsb=0){
        empty($tsa) && $tsa=time();
        empty($tsb) && $tsb=time();
        $tsaDateArr = getdate($tsa);
        $tsbDateArr = getdate($tsb);
        $dateDiff = date_diff(date_create("${tsaDateArr['year']}-${tsaDateArr['mon']}-${tsaDateArr['mday']}"), date_create("${tsbDateArr['year']}-${tsbDateArr['mon']}-${tsbDateArr['mday']}"),TRUE);
        return $dateDiff->days;
    }

    /**
     * 获取时间戳加日期数后的时间戳
     * @param int $tsa
     * @param int $interval
     */
    public static function addDayNum($tsa=0,$interval=0){
        empty($tsa) && $tsa=time();
        $tsaDateArr = getdate($tsa);
        $rdate = date_create("${tsaDateArr['year']}-${tsaDateArr['mon']}-${tsaDateArr['mday']}");
        if($interval>0){
            $rdate->add(new DateInterval('P'.$interval.'D'));
        }
        return $rdate->getTimestamp();
    }

    /**
     * 获取时间戳减日期数后的时间戳
     * @param int $tsa
     * @param int $interval
     */
    public static function subDayNum($tsa=0,$interval=0){
        empty($tsa) && $tsa=time();
        $tsaDateArr = getdate($tsa);
        $rdate = date_create("${tsaDateArr['year']}-${tsaDateArr['mon']}-${tsaDateArr['mday']}");
        if($interval>0){
            $rdate->sub(new DateInterval('P'.$interval.'D'));
        }
        return $rdate->getTimestamp();
    }

    /**
     * 检测是否包含逻辑运算操作符号
     *
     * @param   string   string to check
     * @return  boolean
     */
    public static function hasOperator($string){
        return (bool) preg_match('/[<>!=]/i', trim($string));
    }
    /**
     * 分离操作符号
     *
     * @param   string   string to check
     * @return  array
     */
    public static function splitOperator($string){
        $string = trim($string);
        $matches = array();
        $retArr = array(
            'key'=>'',
            'operator'=>'',
        );
        if(preg_match('/[<>!=]/i', $string) && preg_match('/([\w]+)\s*([<>!=]+)/i',$string,$matches)){
            $key = $matches[1];
            $operator = $matches[2];
            $retArr['key'] = $key;
            $retArr['operator'] = $operator;
        }else{
            $retArr['key'] = $string;
        }
        return $retArr;
    }

    /**
     * 当前时间
     * @return number
     */
    public static function microtimeFloat()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    public static function unserializeSessionData($session_data=''){
        $return_data = array();
        $offset = 0;
        while ($offset < strlen($session_data)) {
            if (!strstr(substr($session_data, $offset), "|")) {
                return NULL;
                //throw new Exception("invalid data, remaining: " . substr($session_data, $offset));
            }
            $pos = strpos($session_data, "|", $offset);
            $num = $pos - $offset;
            $varname = substr($session_data, $offset, $num);
            $offset += $num + 1;
            $data = unserialize(substr($session_data, $offset));
            $return_data[$varname] = $data;
            $offset += strlen(serialize($data));
        }
        return $return_data;
    }
    public static function unserializeSessionDataBinary($session_data=''){
        $return_data = array();
        $offset = 0;
        while ($offset < strlen($session_data)) {
            $num = ord($session_data[$offset]);
            $offset += 1;
            $varname = substr($session_data, $offset, $num);
            $offset += $num;
            $data = unserialize(substr($session_data, $offset));
            $return_data[$varname] = $data;
            $offset += strlen(serialize($data));
        }
        return $return_data;
    }
}