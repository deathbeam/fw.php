<?php
if (!isset($fw)) {
	abstract class Plugin {
		public static function getInstance() {
			static $instance = null;
			if (null === $instance) $instance = new static();
			return $instance;
		}

		public function init($less) { }
		protected function __construct() { }
		private function __clone() { }
		private function __wakeup() { }
	}

	class Base {
		const
			METHODS = 'GET|HEAD|POST|PUT|PATCH|DELETE|CONNECT';
		
		protected 
			$stack,
			$plugins,
			$routes,
			$default_route;

		public static function getInstance() {
			static $instance = null;
			if (null === $instance) $instance = new static();
			return $instance;
		}

		protected function __construct() {
			error_reporting(E_ALL);
			ini_set("display_errors", 1);
			ini_set('default_charset', $charset='UTF-8');
			if (extension_loaded('mbstring')) mb_internal_encoding($charset);
			$url = implode('/',array_map('urlencode',explode('/',rtrim(dirname($_SERVER['SCRIPT_NAME']),'/'))));
			$uri = preg_replace('/^'.preg_quote($url,'/').'/','',parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH));
			$default_route = function() { echo '<h1>404!</h1> Page not found.'; };
			
			$this->stack = array (
				'TIME' => microtime(TRUE),
				'ENCODING'=> $charset,
				'URL' => $url,
				'URI' => $uri,
				'METHOD' => $_SERVER['REQUEST_METHOD'],
				'PUBLIC_DIR' => 'public/',
				'PLUGIN_DIR' => 'plugins/'
			);
		}

		public function __set($name, $value) {
			if (isset($this->plugins[$name])) throw new InvalidArgumentException("Plugin '$name' is already loaded.");
			$this->plugins[$name] = include $this->stack['PLUGIN_DIR'].$value;
			$this->plugins[$name]->init($this);
			return $this;
		}

		public function __get($name) {
			if (!isset($this->plugins[$name])) throw new InvalidArgumentException("Plugin '$name' is not loaded.");
			return $this->plugins[$name];
		}

		public function set($name, $value) {
			$this->stack[$name] = $value;
			if ($name == 'ENCODING') {
				$value = ini_set('default_charset', $value);
				if (extension_loaded('mbstring')) mb_internal_encoding($value);
			}
			return $this;
		}

		public function get($name) {
			if (!isset($this->stack[$name])) throw new InvalidArgumentException("Unable to get the field '$name'.");
			return $this->stack[$name];
		}

		public function exists($name) {
			return isset($this->stack[$name]);
		}

		public function clear($name) {
			if (!isset($this->stack[$name])) throw new InvalidArgumentException("Unable to unset the field '$field'.");
			unset($this->stack[$name]);
			return $this;
		}

		public function toArray() {
			return $this->stack;
		}

		public function config($file = null) {
			$config = json_decode(file_get_contents($file),true);
			if (isset($config['globals'])) foreach ($config['globals'] as $key => $value) $this->set($key, $value);
			if (isset($config['plugins'])) foreach ($config['plugins'] as $key => $value) $this->{strtr($key,array(' '=>''))} = strtr($value,array(' '=>''));
			if (isset($config['routes'])) foreach ($config['routes'] as $key => $value) $this->route($key, $value);
			return $this;
		}

		public function draw($file) {
			extract($this->stack);
			ob_start();
			include $this->stack['PUBLIC_DIR'].$file;
			$html = ob_get_clean();
			$path = $this->stack['URL'].'/'.$this->stack['PUBLIC_DIR'];
			echo preg_replace(
				array(
					'/<img(.*?)src=(?:")(http|https)\:\/\/([^"]+?)(?:")/i','/<img(.*?)src=(?:")([^"]+?)#(?:")/i','/<img(.*?)src="(.*?)"/', '/<img(.*?)src=(?:\@)([^"]+?)(?:\@)/i',
					'/<script(.*?)src=(?:")(http|https)\:\/\/([^"]+?)(?:")/i','/<script(.*?)src=(?:")([^"]+?)#(?:")/i','/<script(.*?)src="(.*?)"/','/<script(.*?)src=(?:\@)([^"]+?)(?:\@)/i',
					'/<link(.*?)href=(?:")(http|https)\:\/\/([^"]+?)(?:")/i','/<link(.*?)href=(?:")([^"]+?)#(?:")/i','/<link(.*?)href="(.*?)"/','/<link(.*?)href=(?:\@)([^"]+?)(?:\@)/i'
				), 
				array(
					'<img$1src=@$2://$3@', '<img$1src=@$2@', '<img$1src="' . $path . '$2"', '<img$1src="$2"',
					'<script$1src=@$2://$3@', '<script$1src=@$2@', '<script$1src="' . $path . '$2"', '<script$1src="$2"',
					'<link$1href=@$2://$3@', '<link$1href=@$2@' , '<link$1href="' . $path . '$2"', '<link$1href="$2"'
				), $html);
		}

		public function route($pattern, $target) {
			$pattern = strtr($pattern,array(' '=>''));
			
			if ($pattern == 'default') {
				$this->default_route = $target;
				return $this;
			}
			
			if (is_object($target)) {
				foreach ((explode('|', self::METHODS)) as $method){
					$this->route($method.' '.$pattern, $class.'->'.strtolower($method));
				}
				return $this;
			}
			
			$arr = explode("/", $pattern, 2);
			$route = '/'.$arr[1];
			$name = null;
			
			if (strpos($arr[0], ':') !== false) {
				$arr = explode(":", $arr[0], 2);
				$name = $arr[0];
			}
			
			$method = $arr[0];
			$this->routes[] = array($method, $route, $target , $name);
			
			return $this;
		}

		public function reroute($pattern, array $params = array()) {
			foreach ($this->routes as $_route) {
				if (isset($_route[1]) and $_route[1] == $pattern) { 
					$route = $_route[1];
					break;
				} elseif (isset($_route[3]) and $_route[3] == $pattern) { 
					$route = $_route[1];
					break;
				}
			}
			if (isset($route)) {
				$url = $this->stack['URL'].$route;

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
			} else {
				$url = $this->stack['URL'];
			}

			header('Location: '.$url);
		}

		public function run() {
			foreach($this->routes as $handler) {
				list($method, $route, $target) = $handler;
				$methods = explode('|', $method);
				$method_match = false;
				foreach($methods as $method) {
					if (strcasecmp($this->stack['METHOD'], $method) === 0) {
						$method_match = true;
						break;
					}
				}
				
				if (!$method_match or 
					!preg_match('/^'.
					preg_replace('/@(\w+\b)/','(?P<\1>[^\/\?]+)',
					str_replace('\*','([^\?]*)',preg_quote($route,'/'))).
					'\/?(?:\?.*)?$/ium',$this->stack['URI'],$params))
					continue;
				
				if (is_string($target)) {
					$target = preg_replace_callback('/@(\w+\b)/',
						function($id) use($params) {
							return isset($params[$id[1]])?$params[$id[1]]:$id[0];
						},
						$target
					);
					if (preg_match('/(.+)\h*(?:->|::)/',$target,$match) && !class_exists($match[1])) {
						return call_user_func_array($this->default_route, array($this, null));
					}
				}
				return call_user_func_array($target, array($this, $params));
			}
			return call_user_func_array($this->default_route, array($this, null));
		}
	}

	$fw = Base::getInstance();
} else {
	$fw->run();
}