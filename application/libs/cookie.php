<?php
class Cookie
{
	private static $instance;
	private $expiry = null;
	private $path = null;
	private $domain = null;
	private $init = false;
	
	private function __construct() {
	}
	
	public static function instance() { 
		if(!self::$instance) { 
			self::$instance = new self(); 
		}
		return self::$instance; 
	}
	
	public function init() {
		if ($this->init == true) return;
		$fw = Base::instance();
		$path = ($fw->exists('COOKIE_PATH') ? $fw->get('COOKIE_PATH') : '/');
		$expiry = ($fw->exists('COOKIE_EXPIRY') ? $fw->get('COOKIE_EXPIRY') : 86400);
		if ($expiry === -1)
			$expiry = 1893456000; // Lifetime = 2030-01-01 00:00:00
		  elseif (is_numeric($expiry))
			$expiry += time();
		  else
			$expiry = strtotime($expiry);
		$this->path = $path;
		$this->expiry = $expiry;
		$this->domain = $_SERVER['HTTP_HOST'];
		$this->init == true;
	}
	
	public function toArray() {
		return $_COOKIE;
	}
  
	public function set($name, $value) {
		$retval = false;
		if (!headers_sent())
		{
		  $retval = @setcookie($name, $value, $this->expiry, $this->path, $this->domain);
		  if ($retval)
			$_COOKIE[$name] = $value;
		}
		return $retval;
    }
     
    public function get($name) {
        if (!isset($_COOKIE[$name])) {
            throw new InvalidArgumentException(
                "Unable to get the field '$name'.");
        }
        $field = $_COOKIE[$name];
        return $field instanceof Closure ? $field($this) : $field;
    }
     
    public function exists($name) {
        return isset($_COOKIE[$name]);
    }
     
    public function clear($name) {
        if (!isset($_COOKIE[$name])) {
            throw new InvalidArgumentException(
                "Unable to unset the field '$name'.");
        }
        unset($_COOKIE[$name]);
        return $this;
    }
}
return Cookie::instance();