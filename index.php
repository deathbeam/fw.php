<?php
$hobo = require('application/base.php');
$hobo->config('config.ini');
$hobo->cookie->init();
$hobo->session->init();
$hobo->db->init();

// Use $hobo->db to fetch data from database
$hobo->db->query('SELECT FName, LName, Age, Gender FROM mytable WHERE LName = :lname');
$hobo->db->bind(':lname', 'Slusny');
$row = $hobo->db->fetchRow();
$hobo->set('user', $row['FName'].' '.$row['LName']);
$hobo->session->set('test','This is session var');
$hobo->cookie->set('test','This is cookie');
	
function index($hobo, $params) {
	// Set global variables what can be used in view
	$hobo->set('header','header.php');
	$hobo->set('footer','footer.php');
	$hobo->set('title','HoboMVC');
	$hobo->set('heading','Hello World!');
	$hobo->set('content','This is Hello World index page using Hobo MVC Framework');
	// Render view
	echo $hobo->render('public/default.php');
}

function about($hobo, $params) {
	// Set global variables what can be used in view
	$hobo->set('header','header.php');
	$hobo->set('footer','footer.php');
	$hobo->set('title','About &middot; HoboMVC');
	$hobo->set('heading','About Hobo');
	$hobo->set('content','Hobo MVC is aiming to be super simple and super intuitive MVC framework. It is inspired by 2 my personally favorite MVC frameworks, F3(fat free) framework and PHP-MVC framework, but Hobo do not have that many features as both of them. Hobo core is currently built on 3 libs, and they are router.php, session.php and view.php. Names of these libs are self-explaining.');
	// Render view
	echo $hobo->render('public/default.php');
}

function error($hobo, $params) {
	// Set global variables what can be used in view
	$hobo->set('header','header.php');
	$hobo->set('footer','footer.php');
	$hobo->set('title','Page not found &middot; HoboMVC');
	$hobo->set('heading','404!');
	$hobo->set('content','This is not the web page you are looking for.');
	// Render view
	echo $hobo->render('public/default.php');
}

$hobo->run();