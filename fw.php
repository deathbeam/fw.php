<?php App::execute();

//! Abstract class for application plugins
abstract class Plugin {

	/**
	*	Return plugin instance
	*	@return static
	**/
	public static function instance() {
		static $instance = null;
		if (null === $instance) $instance = new static();
		return $instance;
	}
	
	/**
	*	Handle plugin initialization
	*	@return NULL
	**/
	public function init($fw) { }
	
	protected function __construct() { }
	private function __clone() { }
	private function __wakeup() { }
}

//! Base structure
class App {

	//@{ Framework details
	const
		PACKAGE='fw.php',
		VERSION='0.5.0-Beta';
	//@}
	
	//@{ Error messages
	const
		E_Index = 
				'Create <code>index.php</code> and start making your first
				<code>fw.php</code> application. If you need help, please read 
				<a href="http://deathbeam.github.io/fwphp/docs.htm">online documentation</a>.',
		E_Stack = 'Invalid stack key %s',
		E_Route='Route does not exist: %s',
		E_Routes = 'No routes specified',
		E_Class='Invalid class %s',
		E_Method='Invalid method %s',
		E_Function='Invalid function %s',
		E_Plugin = 'Invalid plugin %s';
	//@}
		
	protected 
		//! Globals
		$stack,
		//! Array of plugins
		$plugins,
		//! Routing table
		$routes,
		//! Default route
		$default_route;
	
	/**
	*	Bootstrap
	*	@return NULL
	**/
	public static function execute() {
		if (file_exists('vendor/autoload.php')) require 'vendor/autoload.php';
		$fw = new App();
		if (!file_exists('index.php'))
			$fw->error(self::E_Index);
		else
			require 'index.php';
		$fw->run();
	}
	
	/**
	*	Set default settings and initialize app
	*	@return NULL
	**/
	protected function __construct() {
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
		ini_set('default_charset', $charset='UTF-8');
		if (extension_loaded('mbstring')) mb_internal_encoding($charset);
		$this->default_route = function() { echo '<h1>404!</h1> Page not found.'; };
		$url = implode('/',array_map('urlencode',explode('/',rtrim(dirname($_SERVER['SCRIPT_NAME']),'/'))));
		$uri = preg_replace('/^'.preg_quote($url,'/').'/','',parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH));
		$method = isset($_SERVER['REQUEST_METHOD'])? $_SERVER['REQUEST_METHOD']: 'GET';

		$this->stack = array (
			'encoding'=> $charset,
			'method' => $method,
			'package' => self::PACKAGE,
			'public_dir' => 'public',
			'plugin_dir' => 'plugins',
			'time' => microtime(TRUE),
			'url' => $url,
			'uri' => $uri,
			'version' => self::VERSION
		);
	}
	
	/**
	*	Display default error page
	*	@return NULL
	*	@param $message string
	*	@param $arg string
	**/
	public function error($message, $arg = null) {
		if (isset($arg)) $message = strtr($message,array('%s'=>$arg));
		echo $message;
		exit();
	}
	
	/**
	*	Loads specified plugin from plugin directory
	*	@return object
	*	@param $plugin string
	*	@param $value string
	**/
	public function __set($plugin, $value) {
		if (isset($this->plugins[$plugin])) $this->error(self::E_Plugin, $plugin);
		$this->plugins[$plugin] = include $this->stack['plugin_dir'].'/'.$value;
		$this->plugins[$plugin]->init($this);
		return $this;
	}
	
	/**
	*	Loads specified plugin from plugin directory
	*	@return object
	*	@param $plugin string
	**/
	public function __get($plugin) {
		if (!isset($this->plugins[$plugin])) $this->error(self::E_Plugin, $plugin);
		return $this->plugins[$plugin];
	}
	
	/**
	*	Bind value to stack key
	*	@return mixed
	*	@param $key string
	*	@param $value mixed
	**/
	public function set($key, $value) {
		$this->stack[$key] = $value;
		if ($key == 'encoding') {
			$value = ini_set('default_charset', $value);
			if (extension_loaded('mbstring')) mb_internal_encoding($value);
		}
		return $this;
	}

	/**
	*	Retrieve contents of stack key
	*	@return mixed
	*	@param $key string
	**/
	public function get($key) {
		if (!isset($this->stack[$key])) $this->error(self::E_Stack, $key);
		return $this->stack[$key];
	}

	/**
	*	Return TRUE if stack key is set
	*	@return bool
	*	@param $key string
	**/
	public function exists($key) {
		return isset($this->stack[$key]);
	}

	/**
	*	Unset stack key
	*	@return object
	*	@param $key string
	**/
	public function clear($key) {
		if (!isset($this->stack[$key])) $this->error(self::E_Stack, $key);
		unset($this->stack[$key]);
		return $this;
	}

	/**
	*	Publish stack contents
	*	@return array
	**/
	public function stack() {
		return $this->stack;
	}

	/**
	*	Configure framework according to .json-style file settings
	*	@return object
	*	@param $file string
	**/
	public function config($file) {
		$config = json_decode(file_get_contents($file),true);
		if (isset($config['globals'])) foreach ($config['globals'] as $key => $value)
			$this->set($key, $value);
		if (isset($config['plugins'])) foreach ($config['plugins'] as $key => $value)
			$this->{strtr($key,array(' '=>''))} = strtr($value,array(' '=>''));
		if (isset($config['routes'])) foreach ($config['routes'] as $key => $value)
			$this->route($key, $value);
		return $this;
	}

	/**
	*	Draw template
	*	@return object
	*	@param $template string
	**/
	public function draw($template) {
		extract(array_map('urldecode', $this->stack));
		ob_start();
		include ($path = $this->stack['public_dir'].'/').$template;
		echo preg_replace(
			array(
				'/<img(.*?)src=(?:")(http|https)\:\/\/([^"]+?)(?:")/i','/<img(.*?)src=(?:")([^"]+?)#(?:")/i','/<img(.*?)src="(.*?)"/', '/<img(.*?)src=(?:\@)([^"]+?)(?:\@)/i',
				'/<script(.*?)src=(?:")(http|https)\:\/\/([^"]+?)(?:")/i','/<script(.*?)src=(?:")([^"]+?)#(?:")/i','/<script(.*?)src="(.*?)"/','/<script(.*?)src=(?:\@)([^"]+?)(?:\@)/i',
				'/<link(.*?)href=(?:")(http|https)\:\/\/([^"]+?)(?:")/i','/<link(.*?)href=(?:")([^"]+?)#(?:")/i','/<link(.*?)href="(.*?)"/','/<link(.*?)href=(?:\@)([^"]+?)(?:\@)/i'
			), 
			array(
				'<img$1src=@$2://$3@', '<img$1src=@$2@', '<img$1src="'.$this->stack['url'].'/'.$path.'$2"', '<img$1src="$2"',
				'<script$1src=@$2://$3@', '<script$1src=@$2@', '<script$1src="'.$this->stack['url'].'/'.$path.'$2"', '<script$1src="$2"',
				'<link$1href=@$2://$3@', '<link$1href=@$2@' , '<link$1href="'.$this->stack['url'].'/'.$path.'$2"', '<link$1href="$2"'
			), ob_get_clean()
		);
		return $this;
	}

	/**
	*	Bind callable to route pattern
	*	@return object
	*	@param $pattern string
	*	@param $callable callback
	**/
	public function route($pattern, $callable) {
		$pattern = strtr($pattern,array(' '=>''));
		
		if ($pattern == 'default') {
			$this->default_route = $callable;
			return $this;
		}

		$arr = explode('/', $pattern, 2);
		$method = $arr[0];
		$route = '/'.$arr[1];
		
		if (strpos($arr[0], ':') !== false) {
			$arr = explode(':', $arr[0], 2);
			$name = $arr[0];
			$method = $arr[1];
		}
		
		if ($method == 'MAP') {
			foreach ((explode('|', 'GET|HEAD|POST|PUT|PATCH|DELETE|CONNECT')) as $method) {
				$this->route((isset($name)?$name.':':null).$method.$route, $callable.'->'.strtolower($method));
			}
			return $this;
		}
		
		$this->routes[] = array($method, $route, $callable, isset($name)?$name:null);
		return $this;
	}

/**
	*	Redirect to specified route
	*	@return NULL
	*	@param $pattern string
	*	@param $params array
	**/
	public function reroute($pattern, array $params = array()) {
		foreach ($this->routes as $_route) {
			if (isset($_route[1]) and $_route[1] == $pattern) { 
				$route = $_route[1]; break;
			} elseif (isset($_route[3]) and $_route[3] == $pattern) { 
				$route = $_route[1]; break;
			}
		}
		
		if (isset($route)) {
			$url = $this->stack['url'].$route;
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
			header('Location: '.$url);
		} else {
			$this->error(self::E_Route, $route);
		}
	}

	/**
	*	Match routes against incoming URI
	*	@return NULL
	**/
	protected function run() {
		if (!isset($this->routes)) $this->error(self::E_Routes);
		$routed = false;
		
		foreach($this->routes as $handler) {
			list($method, $route, $callable) = $handler;
			$method_match = false;

			foreach(explode('|', $method) as $method) {
				if (strcasecmp($this->stack['method'], $method) === 0) {
					$method_match = true; break;
				}
			}

			if (!$method_match or 
				!preg_match('/^'.
				preg_replace('/@(\w+\b)/','(?P<\1>[^\/\?]+)',
				str_replace('\*','([^\?]*)',preg_quote($route,'/'))).
				'\/?(?:\?.*)?$/ium',$this->stack['uri'],$params))
				continue;

			if (strpos($route,'/*') !== false) {
				foreach (array_keys($params) as $key)
					if (is_numeric($key) && $key)
						unset($params[$key]);
			}

			if (is_string($callable)) {
				$callable = preg_replace_callback('/@(\w+\b)/',
					function($id) use($params) {
						return isset($params[$id[1]])?$params[$id[1]]:$id[0];
					},
					$callable
				);
				
				if (preg_match('/(.+)\h*(?:->|::)(.+)\h*/', $callable, $match)) {
					if (!class_exists($match[1])) {
						$this->error(self::E_Class, $match[1]);
					} elseif (!method_exists($match[1],$match[2])) {
						$this->error(self::E_Method, $match[2]);
					}
					$callable = array($match[1], $match[2]);
				}

				if (!function_exists($callable)) $this->error(self::E_Function, $callable);
			}

			@call_user_func_array($callable, array($this, $params));
			$routed = true;
			break;
		}
		
		if (!$routed) @call_user_func_array($this->default_route, array($this, null));
	}
}