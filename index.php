<?php
$hobo = require('application/base.php');
$hobo->config('config.ini');

function index() {
	$view = new View();
	$view->attach('public/header.php');
	$view->attach('public/default.php');
	$view->attach('public/footer.php');
	$view->title = "HoboMVC";
	$view->heading = "Hello World!";
	$view->content = 'This is Hello World index page using Hobo MVC Framework';
	echo $view->render();
}

function about() {
	$view = new View();
	$view->attach('public/header.php');
	$view->attach('public/default.php');
	$view->attach('public/footer.php');
	$view->title = "About &middot; HoboMVC";
	$view->heading = "About Hobo";
	$view->content = 'Hobo MVC is aiming to be super simple and super intuitive MVC framework. It is inspired by 2 my personally favorite MVC frameworks, F3(fat free) framework and PHP-MVC framework, but Hobo do not have that many features as both of them. Hobo core is currently built on 3 libs, and they are router.php, session.php and view.php. Names of these libs are self-explaining.';
	echo $view->render();
}

function error() {
	$view = new View();
	$view->attach('public/header.php');
	$view->attach('public/default.php');
	$view->attach('public/footer.php');
	$view->title = "Page not found &middot; HoboMVC";
	$view->heading = "404!";
	$view->content = 'This is not the web page you are looking for.';
	echo $view->render();
}

$hobo->route('/', 'index');
$hobo->route('/about', 'about');
$hobo->default_route('error');
$hobo->run();