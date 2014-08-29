<?php
return View::getInstance();

class View extends Library {
	private $libs = array();
	private $fields = array();
	private $path = 'public/views/';
	
	public function init($hobo) {
		$this->libs = &$hobo->getLibs();
		$this->fields = &$hobo->getFields();
	}
	
	public function draw($file) {
		extract($this->fields);
		foreach($this->libs as $key => $value) {
			$arr = $value->toArray();
			if (!is_null($arr)) $libs[$key] = $arr;
		}
		extract($libs);
		unset($libs);
        ob_start();
		include $this->path.$file;
        echo ob_get_clean();
    }
}