<?php
if (file_exists('../vendor/autoload.php')) require '../vendor/autoload.php';
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
	protected $default_route = null;
	protected $fields = array();
	protected $libs = array();
	protected $routes = array();
	protected $namedRoutes = array();
	protected $matchTypes = array(
		'i'  => '[0-9]++',
		'a'  => '[0-9A-Za-z]++',
		'h'  => '[0-9A-Fa-f]++',
		'*'  => '.+?',
		'**' => '.++',
		''   => '[^/\.]++'
	);
	
	public static function getInstance() {
		static $instance = null;
		if (null === $instance) $instance = new static();
		return $instance;
	}
	
	protected function __construct() {
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
		echo preg_replace(
			array(
				'/<img(.*?)src=(?:")(http|https)\:\/\/([^"]+?)(?:")/i','/<img(.*?)src=(?:")([^"]+?)#(?:")/i','/<img(.*?)src="(.*?)"/', '/<img(.*?)src=(?:\@)([^"]+?)(?:\@)/i',
				'/<script(.*?)src=(?:")(http|https)\:\/\/([^"]+?)(?:")/i','/<script(.*?)src=(?:")([^"]+?)#(?:")/i','/<script(.*?)src="(.*?)"/','/<script(.*?)src=(?:\@)([^"]+?)(?:\@)/i',
				'/<link(.*?)href=(?:")(http|https)\:\/\/([^"]+?)(?:")/i','/<link(.*?)href=(?:")([^"]+?)#(?:")/i','/<link(.*?)href="(.*?)"/','/<link(.*?)href=(?:\@)([^"]+?)(?:\@)/i',
				'/<a(.*?)href=(?:")(http\:\/\/|https\:\/\/|javascript:)([^"]+?)(?:")/i', '/<a(.*?)href="(.*?)"/', '/<a(.*?)href=(?:\@)([^"]+?)(?:\@)/i',
				'/<input(.*?)src=(?:")(http|https)\:\/\/([^"]+?)(?:")/i', '/<input(.*?)src=(?:")([^"]+?)#(?:")/i', '/<input(.*?)src="(.*?)"/', '/<input(.*?)src=(?:\@)([^"]+?)(?:\@)/i'
			), 
			array(
				'<img$1src=@$2://$3@', '<img$1src=@$2@', '<img$1src="' . $this->get('URL').$this->get('PUBLIC_DIR') . '$2"', '<img$1src="$2"',
				'<script$1src=@$2://$3@', '<script$1src=@$2@', '<script$1src="' . $this->get('URL').$this->get('PUBLIC_DIR') . '$2"', '<script$1src="$2"',
				'<link$1href=@$2://$3@', '<link$1href=@$2@' , '<link$1href="' . $this->get('URL').$this->get('PUBLIC_DIR') . '$2"', '<link$1href="$2"',
				'<a$1href=@$2$3@', '<a$1href="' . $this->get('URL') . '$2"', '<a$1href="$2"',
				'<input$1src=@$2://$3@', '<input$1src=@$2@', '<input$1src="' . $this->get('URL') . '$2"', '<input$1src="$2"'
			), ob_get_clean());
	}
  
	public function route($pattern, $callback) {
		$pattern = strtr($pattern,array(' '=>''));
		
		if ($pattern == 'default') {
			$this->default_route = $callback;
			return $this;
		}
		
		$arr = explode("/", $pattern, 2);
		$route = '/'.$arr[1];
		$name = null;
		
		if (strpos($arr[0], '@') !== false) {
			$arr = explode("@", $arr[0], 2);
			$name = $arr[1];
		}
		
		$method = $arr[0];
		$this->routes[] = array($method, $route, $callback, $name);
		
		if($name) {
			if(isset($this->namedRoutes[$name])) {
				throw new Exception("Can not redeclare route '{$name}'");
			} else {
				$this->namedRoutes[$name] = $route;
			}
		}
		
		return $this;
	}
	
	public function generate($routeName, array $params = array()) {
		if(!isset($this->namedRoutes[$routeName])) {throw new InvalidArgumentException("Route '{$routeName}' does not exist.");}
		$route = $this->namedRoutes[$routeName];
		$url = substr(parse_url($this->get('URL'))['path'], 0, -1).$route;

		if (preg_match_all('`(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`', $route, $matches, PREG_SET_ORDER)) {
			foreach($matches as $match) {
				list($block, $pre, $type, $param, $optional) = $match;
				if ($pre) $block = substr($block, 1);

				if(isset($params[$param])) {
					$url = str_replace($block, $params[$param], $url);
				} elseif ($optional) {
					$url = str_replace($pre . $block, '', $url);
				}
			}
		}

		return $url;
	}
	
	public function run() {
		$params = array();
		$match = false;

		$requestUrl = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
		$requestUrl = substr($requestUrl, strlen(substr(parse_url($this->get('URL'))['path'], 0, -1)));

		if (($strpos = strpos($requestUrl, '?')) !== false) $requestUrl = substr($requestUrl, 0, $strpos);

		$requestMethod = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
		$_REQUEST = array_merge($_GET, $_POST);

		foreach($this->routes as $handler) {
			list($method, $_route, $target, $name) = $handler;

			$methods = explode('|', $method);
			$method_match = false;
			
			foreach($methods as $method) {
				if (strcasecmp($requestMethod, $method) === 0) {
					$method_match = true;
					break;
				}
			}
			
			if(!$method_match) continue;

			if ($_route === '*') {
				$match = true;
			} elseif (isset($_route[0]) && $_route[0] === '@') {
				$pattern = '`' . substr($_route, 1) . '`u';
				$match = preg_match($pattern, $requestUrl, $params);
			} else {
				$route = null;
				$regex = false;
				$j = 0;
				$n = isset($_route[0]) ? $_route[0] : null;
				$i = 0;

				while (true) {
					if (!isset($_route[$i])) {
						break;
					} elseif (false === $regex) {
						$c = $n;
						$regex = $c === '[' || $c === '(' || $c === '.';
						if (false === $regex && false !== isset($_route[$i+1])) {
							$n = $_route[$i + 1];
							$regex = $n === '?' || $n === '+' || $n === '*' || $n === '{';
						}
						if (false === $regex && $c !== '/' && (!isset($requestUrl[$j]) || $c !== $requestUrl[$j])) {
							continue 2;
						}
						$j++;
					}
					$route .= $_route[$i++];
				}
				
				if (preg_match_all('`(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`', $route, $matches, PREG_SET_ORDER)) {
					foreach($matches as $match) {
						list($block, $pre, $type, $param, $optional) = $match;
						if (isset($matchTypes[$type])) $type = $this->matchTypes[$type];
						if ($pre === '.') $pre = '\.';

						$route = str_replace(
							$block,
							'(?:'.($pre !== '' ? $pre : null).'('.($param !== '' ? "?P<$param>" : null).$type.'))'.($optional !== '' ? '?' : null),
							$route);
					}
				}
				
				$regex = "`^$route$`u";
				$match = preg_match($regex, $requestUrl, $params);
			}

			if(($match == true || $match > 0)) {
				if($params) {
					foreach($params as $key => $value) {
						if(is_numeric($key)) unset($params[$key]);
					}
				}
				call_user_func_array($target, array('hobo' => $this, 'params' => $params));
				return;
			}
		}
		call_user_func_array($this->default_route, array('hobo' => $this, 'params' => array()));
	}
}