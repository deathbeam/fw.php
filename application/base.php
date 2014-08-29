<?php
require_once 'router.php';
return Base::getInstance();

abstract class Library {
    public static function getInstance() {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }
	public function toArray() {
		return null;
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
	
	protected function __construct() {
		$this->router = new AltoRouter();
	}
	
	public static function getInstance() {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }
	
	public function apply() {
		if ($this->exists('URL')) {
			$url=parse_url($this->get('URL'));
			$this->router->setBasePath(substr($url['path'], 0, -1));
		}
		foreach($this->libs as $key => $value) $value->init($this);
	}
	
	public function __set($name, $value) {
		if (!isset($this->libs[$name])) $this->libs[$name] = include 'libs/'.$value;
		return $this;
    }
	
	public function __get($name) {
		return $this->libs[$name];
    }
	
	public function &getLibs() {
		return $this->libs;
    }
	
	public function &getFields() {
		return $this->fields;
	}
	
	function config($file) {
		$string = file_get_contents($file);
		$config=json_decode($string,true);
		if (isset($config['globals'])) {
			foreach ($config['globals'] as $key => $value) $this->set($key, $value);
		}
		if (isset($config['routes'])) {
			foreach ($config['routes'] as $key => $value) $this->route($key, $value);
		}
		if (isset($config['libs'])) {
			foreach ($config['libs'] as $key => $value) {
				$key = preg_replace('/\s+/', '', $key);
				$value = preg_replace('/\s+/', '', $value);
				$this->$key = $value;
			}
		}
	}
  
	public function route($pattern, $callback) {
		$callback = preg_replace('/\s+/', '',$callback);
		$pattern = preg_replace('/\s+/', '',$pattern);
		if ($pattern == 'default') {
			$this->default_route = $callback;
			return true;
		}
		$arr = explode("/", $pattern, 2);
		$method = $arr[0];
		$route = '/'.$arr[1];
		$this->router->map($method,$route,$callback);
		return true;
	}
	
	public function set($name, $value) {
		$this->fields[$name] = $value;
        return $this;
    }
     
    public function get($name) {
        if (!isset($this->fields[$name])) {
            throw new InvalidArgumentException(
                "Unable to get the field '$name'.");
        }
        $field = $this->fields[$name];
        return $field instanceof Closure ? $field($this) : $field;
    }
     
    public function exists($name) {
        return isset($this->fields[$name]);
    }
     
    public function clear($name) {
        if (!isset($this->fields[$name])) {
            throw new InvalidArgumentException(
                "Unable to unset the field '$field'.");
        }
        unset($this->fields[$name]);
        return $this;
    }
	
	public function run() {
		// Execute functions based on route match
		$match = $this->router->match();
		if($match) {
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