<?php
if (file_exists('../vendor/autoload.php')) {
    require '../vendor/autoload.php';
}

require 'libs/router.php';
require 'libs/view.php';
require 'libs/session.php';

class Base {
	private static $instance;
	private $router = null;
	private $fields = array();
	public $db = null;
	public $session = null;
	
	public function __construct() {
		$this->router = Router::instance();
		$this->session = Session::instance();
	}
	
	public static function instance() { 
		if(!self::$instance) { 
			self::$instance = new self(); 
		}
		return self::$instance; 
	} 

    private function openDatabaseConnection() {
		if (!($this->exists('DB_TYPE')) or 
			!($this->exists('DB_HOST')) or
			!($this->exists('DB_NAME')) or
			!($this->exists('DB_USER')) or
			!($this->exists('DB_PASS'))) {
			return;
		}
		$options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING);
		try {
			$this->db = new PDO(
				$this->get('DB_TYPE').':host='.
				$this->get('DB_HOST').';dbname='.
				$this->get('DB_NAME'), 
				$this->get('DB_USER'), 
				$this->get('DB_PASS'), $options);
		} catch (Exception $e) {
			throw new Exception("Database not exists or its configuration is wrong.");
		}
    }
	
	function config($file) {
		$config = parse_ini_file($file);
		foreach ($config as $key => $value) {
			$this->set($key, $value);
		}
	}
	
	public function fields() {
		return $this->fields;
	}
	
	public function default_route($callback) {
		$this->router->default_route($callback);
	}
  
	public function route($route, $callback) {
		$this->router->route($route, $callback);
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
		$this->openDatabaseConnection();
		$this->router->execute();
	}
}

return Base::instance();