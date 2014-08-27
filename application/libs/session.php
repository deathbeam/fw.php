<?php
class Session {
	private static $instance;
	private $name = false;
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
		if ($fw->exists('SESSION_NAME')) {
			session_name($fw->get('SESSION_NAME'));
		}
		session_start();
		$this->init == true;
	}
	
	public function toArray() {
		return $_SESSION;
	}
	
	public function set($name, $value) {
        $_SESSION[$name] = $value;
        return $this;
    }
     
    public function get($name) {
        if (!isset($_SESSION[$name])) {
            throw new InvalidArgumentException(
                "Unable to get the field '$name'.");
        }
        $field = $_SESSION[$name];
        return $field instanceof Closure ? $field($this) : $field;
    }
     
    public function exists($name) {
        return isset($_SESSION[$name]);
    }
     
    public function clear($name) {
        if (!isset($_SESSION[$name])) {
            throw new InvalidArgumentException(
                "Unable to unset the field '$name'.");
        }
        unset($_SESSION[$name]);
        return $this;
    }
}
return Session::instance();