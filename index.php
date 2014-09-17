<?php
$fw->route('GET /', function($fw, $params) {
	$fw
		->set('title','Powered by fw.php')
		->set('heading','code less, create more')
		->set('content','Congratulations! Your fw.php application is running.')
		->draw('default.php');
});