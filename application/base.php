<?php
abstract class Prefab {
	protected $init = false;
    public static function getInstance() {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }
	abstract public function init();
    protected function __construct() { }
    private function __clone() { }
    private function __wakeup() { }
}

class Base extends Prefab {
	private static $instance;
	private $default_route = null;
	private $fields = array();
	private $registry = array();
	
	protected function __construct() {
		require 'autoload.php';
	}
	
	public function init() {
		if ($this->init == true) return;
		foreach($this->registry as $key => $value) $value->init();
		$this->init = true;
	}
	
	public function __set($name, $value) {
		$this->registry[$name] = $value;
		return $this;
    }
	
	public function __get($name) {
		return $this->registry[$name];
    }
	
	function config($file) {
		$config = parse_ini_file($file, true);
		if (isset($config['globals'])) {
			foreach ($config['globals'] as $key => $value) {
				$this->set($key, $value);
			}
		}
		if (isset($config['routes'])) {
			foreach ($config['routes'] as $key => $value) {
				$target = preg_replace('/\s+/', '', $value);
				$this->route($key, $target);
			}
		}
	}
	
	public function toArray() {
		return $this->fields;
	}
	
	public function default_route($callback) {
		$this->default_route = $callback;
	}
  
	public function route($pattern, $callback) {
		$arr = explode("/", $pattern, 2);
		$method = preg_replace('/\s+/', '',$arr[0]);
		$route = '/'.preg_replace('/\s+/', '',$arr[1]);
		$this->router->map($method,$route,$callback);
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
	
	public function render($file) {
		$session = $this->session->toArray();
		$cookie = $this->cookie->toArray();
		extract($this->toArray());
        ob_start();
		include $file;
        return ob_get_clean();
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

return Base::getInstance();