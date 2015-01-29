<?php
/**
 * Description
 *
 * @project appbox
 * @package
 * @author nickfan<nickfan81@gmail.com>
 * @link http://www.axiong.me
 * @version $Id$
 * @lastmodified: 2015-01-28 23:09
 *
 */



namespace Nickfan\BoxApp\BoxView;

use ArrayAccess;
use Closure;
class BoxView implements ArrayAccess,BoxViewInterface{

    public $basePath = '';
    protected $view = null;
    protected $viewPath = '';
    public $data = array();

    public function __construct($basePath = '') {
        $this->basePath = $basePath;
    }

    public function setBasePath($basePath=''){
        $this->basePath = $basePath;
    }

    public function getBasePath(){
        return $this->basePath;
    }

    public function make($viewName='',$data=array()){

        if(!$viewName){
            throw new \InvalidArgumentException('View name can not be empty');
        }
        $this->viewPath = $this->getFilePath($viewName);
        if(!empty($data)){
            $this->data = array_merge($this->data,$data);
        }
        return $this;
    }

    protected function getFilePath($viewName){
        $filePath = str_replace('.', '/', $viewName);
        $realFilePath = $this->basePath.'/'.$filePath.'.php';
        if(is_file($realFilePath)){
            return $realFilePath;
        }else{
            throw new \UnexpectedValueException("View file does not exist!");
        }
    }

    protected function loadView($viewPath='', $data=array()){
        if ($viewPath == '')
            return ;

        // Buffering on
        ob_start();

        // Import the view variables to local namespace
        extract($data, EXTR_SKIP);
        try{
            include $viewPath;
        }catch (\Exception $ex){
            ob_end_clean();
            throw $ex;
        }

        // Fetch the output and close the buffer
        return ob_get_clean();
    }

    /**
     * Get the string contents of the view.
     *
     * @param  \Closure  $callback
     * @return string
     */
    public function renderView(\Closure $callback = null)
    {
        $contents = $this->loadView($this->viewPath,$this->data);

        $response = isset($callback) ? $callback($this, $contents) : null;

        return $response ?$response: $contents;
    }

    public function render($print = true,\Closure $callback = null){
        if($print==true){
            echo $this->renderView($callback);
            return ;
        }else{
            return $this->renderView($callback);
        }
    }
    public function with($key, $value = null){
        $this->data[$key] = $value;
        return $this;
    }


    public function __call($method, $parameters){
        if (starts_with($method, 'with'))
        {
            return $this->with(snake_case(substr($method, 4)), $parameters[0]);
        }
        throw new \BadMethodCallException("Function [$method] does not exist!");
    }

    public function toArray(){
        return $this->data;
    }

    public function toJson($options = 0){
        return json_encode($this->toArray(), $options);
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
        return array_key_exists($offset, $this->data);
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
        return $this->data[$offset];
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
        $this->with($offset, $value);
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
        unset($this->data[$offset]);
    }

    function &__get($name) {
        return $this->data[$name];
    }

    function __set($name, $value) {
        $this->with($name, $value);
    }

    function __isset($name) {
        return isset($this->data[$name]);
    }

    function __unset($name) {
        unset($this->data[$name]);
    }

    function __toString() {
        return $this->render(false);
    }
}
