<?php
class Session extends Prefab {
	private $name = false;
	
	public function init() {
		$fw = Base::getInstance();
		if ($fw->exists('SESSION_NAME')) {
			session_name($fw->get('SESSION_NAME'));
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
return Session::getInstance();