<?php
return Cookie::getInstance();

class Cookie extends Library {
	private $expiry = null;
	private $path = null;
	private $domain = null;
	
	public function init($hobo) {
		$path = ($hobo->exists('COOKIE_PATH') ? $hobo->get('COOKIE_PATH') : '/');
		$expiry = ($hobo->exists('COOKIE_EXPIRY') ? $hobo->get('COOKIE_EXPIRY') : 86400);
		if ($expiry === -1)
			$expiry = 1893456000; // Lifetime = 2030-01-01 00:00:00
		  elseif (is_numeric($expiry))
			$expiry += time();
		  else
			$expiry = strtotime($expiry);
		$this->path = $path;
		$this->expiry = $expiry;
		$this->domain = $_SERVER['HTTP_HOST'];
	}
	
	public function toArray() {
		return $_COOKIE;
	}
  
	public function set($name, $value) {
		if (!headers_sent()) {
		  $retval = @setcookie($name, $value, $this->expiry, $this->path, $this->domain);
		  if ($retval) $_COOKIE[$name] = $value;
		}
		return $this;
    }
     
    public function get($name) {
		if (!isset($_COOKIE[$name])) throw new InvalidArgumentException("Unable to get the field '$name'.");
        return  $_COOKIE[$name];
    }
     
    public function exists($name) {
        return isset($_COOKIE[$name]);
    }
     
    public function clear($name) {
        if (!isset($_COOKIE[$name])) throw new InvalidArgumentException("Unable to get the field '$name'.");
        unset($_COOKIE[$name]);
        return $this;
    }
}