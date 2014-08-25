<?php
$hobo = require('application/base.php');

function index() {
	$view = new View('public/default.php');
	$view->title = "HoboMVC";
	$view->content = '
	<div id="github-commits"></div>';
	echo $view->render();
}

function usage() {
	$view = new View('public/default.php');
	$view->title = "Usage &middot; HoboMVC";
	$view->content = '
	<h2 class="page-header">Example usage</h2>
	<pre>
$hobo = require("application/base.php");

function index() {
	echo "Hello world.";
}

function error() {
	echo "404! This is not the web page you are looking for.;
}

$hobo->route("/", "index");
$hobo->default_route("error");
$hobo->run();</pre>';
	echo $view->render();
}

function error() {
	$view = new View('public/default.php');
	$view->title = "Page not found &middot; HoboMVC";
	$view->content = '
	<div class="text-center">
		<h1>404!</h1>
		<h3>This is not the web page you are looking for.</h3>
	</div>';
	echo $view->render();
}

$hobo->route('/', 'index');
$hobo->route('/usage', 'usage');
$hobo->default_route('error');
$hobo->run();