<?php
// Load Hobo base
$hobo = require('application/base.php');
// Load configuration
$hobo->config('config.json');
// Initialize libs
$hobo->init();

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
	echo $hobo->view->render('public/default.php');
}

function about($hobo, $params) {
	// Set global variables what can be used in view
	$hobo->set('header','header.php');
	$hobo->set('footer','footer.php');
	$hobo->set('title','About &middot; HoboMVC');
	$file = file_get_contents('README.md');
	$content = $hobo->md->text($file);
	$hobo->set('content',$content);
	// Render view
	echo $hobo->view->render('public/about.php');
}

function error($hobo, $params) {
	// Set global variables what can be used in view
	$hobo->set('header','header.php');
	$hobo->set('footer','footer.php');
	$hobo->set('title','Page not found &middot; HoboMVC');
	$hobo->set('heading','404!');
	$hobo->set('content','This is not the web page you are looking for.');
	// Render view
	echo $hobo->view->render('public/default.php');
}

$hobo->run();