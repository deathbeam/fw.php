<?php
class Session {
	private static $instance;
	
	private function __construct() {
	}
	
	public static function instance() { 
		if(!self::$instance) { 
			self::$instance = new self(); 
		}
		return self::$instance; 
	}
	
	/**
    * Starts new or resumes existing session
	*/
    public function start() {
		if(session_start()) {
			return true;
		}
		return false;
	}

	/**
	* End existing session, destroy, unset and delete session cookie
	*/
	public function end() {
		if($this->status != true) {
			$this->start();
		}

		session_destroy();
		session_unset();
		setcookie(session_name(), null, 0, "/");
	}
	
	public function __set($name, $value) {
        $_SESSION[$name] = $value;
        return $this;
    }
     
    public function __get($name) {
        if (!isset($_SESSION[$name])) {
            throw new InvalidArgumentException(
                "Unable to get the field '$field'.");
        }
        $field = $_SESSION[$name];
        return $field instanceof Closure ? $field($this) : $field;
    }
     
    public function __isset($name) {
        return isset($_SESSION[$name]);
    }
     
    public function __unset($name) {
        if (!isset($_SESSION[$name])) {
            throw new InvalidArgumentException(
                "Unable to unset the field '$field'.");
        }
        unset($_SESSION[$name]);
        return $this;
    }
}