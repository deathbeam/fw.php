<?php
return Cookie::instance();

class Cookie extends Plugin {
	private $expiry = 86400;
	private $path = '/';
	private $domain = null;
	
	public function init($fw) {
		$this->domain = $_SERVER['HTTP_HOST'];
		if (!$fw->exists('cookie_config')) return;
		$config = $fw->get('cookie_config');
		$expiry = $config[1]
		if ($expiry === -1)
			$expiry = 1893456000; // Lifetime = 2030-01-01 00:00:00
		  elseif (is_numeric($expiry))
			$expiry += time();
		  else
			$expiry = strtotime($expiry);
		$this->path = $config[0];
		$this->expiry = $expiry;
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