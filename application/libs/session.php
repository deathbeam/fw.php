<?php
return Session::getInstance();

class Session extends Library {
	private $name = false;
	
	public function init($hobo) {
		$hobo = Base::getInstance();
		if ($hobo->exists('SESSION_NAME')) {
			session_name($hobo->get('SESSION_NAME'));
		}
		session_start();
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