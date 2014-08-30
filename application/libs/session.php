<?php
return Session::getInstance();

class Session extends Library {
	private $name = false;
	
	public function init($hobo) {
		if (!$hobo->exists('session_config')) return;
		$config = $hobo->get('session_config');
		session_name($config[0]);
	}
	
	public function toArray() {
		return $_SESSION;
	}
	
	public function start() {
		return session_start();
	}
	
	public function end() {
		session_destroy();
		session_unset();
		setcookie(session_name(), null, 0, "/");
	}
	
	public function set($name, $value) {
        $_SESSION[$name] = $value;
        return $this;
    }
     
    public function get($name) {
        if (!isset($_SESSION[$name])) throw new InvalidArgumentException("Unable to get the field '$name'.");
        return $_SESSION[$name];
    }
     
    public function exists($name) {
        return isset($_SESSION[$name]);
    }
     
    public function clear($name) {
        if (!isset($_SESSION[$name])) throw new InvalidArgumentException("Unable to get the field '$name'.");
        unset($_SESSION[$name]);
        return $this;
    }
}