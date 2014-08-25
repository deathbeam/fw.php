<?php
class View
{
    protected $file = null;
    protected $fields = array();
     
    public function __construct($template, array $fields = array()) {
		$this->template = $template;
        if (!is_file($template) || !is_readable($template)) {
            throw new InvalidArgumentException(
                "The template '$this->template' is invalid.");   
        }
        if (!empty($fields)) {
            foreach ($fields as $name => $value) {
                $this->$name = $value;
            }
        } 
    }
       
    public function getTemplate() {
        return $this->template;
    }
     
    public function __set($name, $value) {
        $this->fields[$name] = $value;
        return $this;
    }
     
    public function __get($name) {
        if (!isset($this->fields[$name])) {
            throw new InvalidArgumentException(
                "Unable to get the field '$field'.");
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
        extract($this->fields);
        ob_start();
        include $this->template;
        return ob_get_clean();
    }
}