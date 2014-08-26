<?php
if (file_exists('../vendor/autoload.php')) {
    require '../vendor/autoload.php';
}
require 'config/config.php';
require 'libs/router.php';
require 'libs/view.php';
require 'libs/session.php';

class Base {
	private static $instance;
	private $router = null;
	public $db = null;
	public $session = null;
	
	public function __construct() {
		$this->router = new Router();
		$this->session = Session::instance();
		if (USE_DB) $this->openDatabaseConnection();
	}
	
	public static function instance() { 
		if(!self::$instance) { 
			self::$instance = new self(); 
		}
		return self::$instance; 
	} 

    private function openDatabaseConnection() {
		$options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING);
        $this->db = new PDO(DB_TYPE . ':host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS, $options);
    }
	
	public function default_route($callback) {
		$this->router->default_route($callback);
	}
  
	public function route($route, $callback) {
		$this->router->route($route, $callback);
	}
	
	public function run() {
		$this->router->execute();
	}
}

return Base::instance();