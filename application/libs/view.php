<?php
class View
{
    protected $files = array();
    protected $fields = array();
     
    public function __construct() {
    }
	
	public function attach($file) {
		if (!is_file($file) || !is_readable($file)) {
            throw new InvalidArgumentException(
                "The template '$this->template' is invalid.");   
        }
        $this->files[$file] = $file;
        return $this;
    }
     
    public function __set($name, $value) {
        $this->fields[$name] = $value;
        return $this;
    }
     
    public function __get($name) {
        if (!isset($this->fields[$name])) {
            throw new InvalidArgumentException(
                "Unable to get the field '$name'.");
        }
        $field = $this->fields[$name];
        return $field instanceof Closure ? $field($this) : $field;
    }
     
    public function __isset($name) {
        return isset($this->fields[$name]);
    }
     
    public function __unset($name) {
        if (!isset($this->fields[$name])) {
            throw new InvalidArgumentException(
                "Unable to unset the field '$field'.");
        }
        unset($this->fields[$name]);
        return $this;
    }
     
    public function render() {
		$fw=Base::instance();
		$globalfields=$fw->fields();
		extract($globalfields);
		unset($fw);
		unset($globalfields);
        extract($this->fields);
        ob_start();
		if (!empty($this->files)) {
            foreach ($this->files as $file) {
                include $file;
            }
        }
        return ob_get_clean();
    }
}