<?php
if (file_exists('../vendor/autoload.php')) {
    require '../vendor/autoload.php';
}

class Base {
	private static $instance;
	private $router = null;
	private $default_route = null;
	private $fields = array();
	public $db = null;
	public $session = null;
	public $cookie = null;
	
	public function __construct() {
		$this->router = require 'libs/router.php';
		$this->session = require 'libs/session.php';
		$this->cookie = require 'libs/cookie.php';
		$this->db = require 'libs/db.php';
	}
	
	public static function instance() { 
		if(!self::$instance) { 
			self::$instance = new self(); 
		}
		return self::$instance; 
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
		// Change subdir of router if URL is set
		if ($this->exists('URL')) {
			$path=parse_url($this->get('URL'));
			$dir=substr($path['path'], 0, -1);
			$this->router->setBasePath($dir);
		}
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
		call_user_func_array($callback, array('hobo' => Base::instance(), 'params' =>$params));
	}
}

return Base::instance();