<?php
$less = require('library/fw.php');
$less->config('config.json')->apply();

function index($less, $params) {
	$less
		->set('title','fw.php')
		->draw('home.php');
}

function error($less, $params) {
	$less
		->set('title','Page not found &middot; fw.php')
		->set('heading','404!')
		->set('content','This is not the web page you are looking for.')
		->draw('default.php');
}

function docs($less, $params) {
	$less
		->set('title','documentation &middot; fw.php')
		->set('heading','documentation')
		->set('content',$less->md->text(file_get_contents('README.md')))
		->draw('default.php');
}

$less->run();