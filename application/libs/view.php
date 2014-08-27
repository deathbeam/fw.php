<?php
class View extends Prefab {
	public function render($file) {
		$fw = Base::getInstance();
		$libs = $fw->getRegistry();
		extract($fw->toArray());
		foreach($libs as $key => $value) {
			$arr = $value->toArray();
			if (!is_null($arr)) $get[$key] = $arr;
		}
		extract($get);
		unset($get);
		unset($fw);
		unset($libs);
        ob_start();
		include $file;
        return ob_get_clean();
    }
}
return View::getInstance();