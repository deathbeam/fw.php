<?php
if (file_exists('../vendor/autoload.php')) require '../vendor/autoload.php';
require 'router.php';
return Base::getInstance();

abstract class Library {
	public static function getInstance() {
		static $instance = null;
		if (null === $instance) $instance = new static();
		return $instance;
	}
	
	public function init($hobo) { }
	protected function __construct() { }
	private function __clone() { }
	private function __wakeup() { }
}

class Base {
	private $default_route = null;
	private $fields = array();
	private $libs = array();
	private $router = null;
	
	public static function getInstance() {
		static $instance = null;
		if (null === $instance) $instance = new static();
		return $instance;
	}
	
	protected function __construct() {
		$this->router = new AltoRouter();
		$this->set('URL', 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']) . "/");
		$this->set('PUBLIC_DIR', 'public/');
	}
	
	public function __set($name, $value) {
		if (!isset($this->libs[$name])) $this->libs[$name] = include 'libs/'.$value;
		return $this;
	}

	public function __get($name) {
		return $this->libs[$name];
	}
	
	public function set($name, $value) {
		$this->fields[$name] = $value;
		return $this;
	}
     
	public function get($name) {
		if (!isset($this->fields[$name])) throw new InvalidArgumentException("Unable to get the field '$name'.");
		$field = $this->fields[$name];
		return $field instanceof Closure ? $field($this) : $field;
	}
	
	public function exists($name) {
		return isset($this->fields[$name]);
	}
    
	public function clear($name) {
		if (!isset($this->fields[$name])) throw new InvalidArgumentException("Unable to unset the field '$field'.");
		unset($this->fields[$name]);
		return $this;
	}
	
	public function apply() {
		$url=parse_url($this->get('URL'));
		$this->router->setBasePath(substr($url['path'], 0, -1));
		foreach($this->libs as $key => $value) $value->init($this);
		return $this;
	}

	public function config($file) {
		$config = json_decode(file_get_contents($file),true);
		if (isset($config['globals'])) foreach ($config['globals'] as $key => $value) $this->set($key, $value);
		if (isset($config['routes'])) foreach ($config['routes'] as $key => $value) $this->route($key, $value);
		if (isset($config['libs'])) foreach ($config['libs'] as $key => $value) $this->{strtr($key,array(' '=>''))} = strtr($value,array(' '=>''));
		return $this;
	}
	
	public function draw($file) {
		extract($this->fields);
		ob_start();
		include $this->get('PUBLIC_DIR').$file;
		echo ob_get_clean();
	}
  
	public function route($pattern, $callback) {
		$pattern = strtr($pattern,array(' '=>''));
		if ($pattern == 'default') {
			$this->default_route = $callback;
			return true;
		}
		$arr = explode("/", $pattern, 2);
		$route = '/'.$arr[1];
		$name = null;
		if (strpos($arr[0], '@') !== false) {
			$arr = explode("@", $arr[0], 2);
			$name = $arr[1];
		}
		$method = $arr[0];
		$this->router->map($method,$route,$callback,$name);
		return $this;
	}
	
	public function generate($route, $params = array()) {
		return $this->router->generate($route, $params);
	}
	
	public function run() {
		if($match = $this->router->match()) {
			$callback = $match['target'];
			$params = $match['params'];
		}
		else {
			$callback = $this->default_route;
			$params = array();
		}
		call_user_func_array($callback, array('hobo' => $this, 'params' =>$params));
	}
}