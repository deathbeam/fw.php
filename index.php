<?php
// Load Hobo base and configuration
$hobo = require('application/base.php');
$hobo->config('config.json')->apply();

// Load example data
$row = $hobo->db->query('SELECT FName, LName, Age, Gender FROM mytable WHERE LName = :lname')->bind(':lname', 'Slusny')->fetchRow();
$hobo->session->start();
$hobo->session->set('test','This is session var');
$hobo->cookie->set('test','This is cookie');

// Here we need to pass them to global variables so we will be able to use them
// in views later. !Important: They are not references, so when you will modifiy 
// for example cookie or session variables, you must re-set them.
$hobo
	->set('user', $row['FName'].' '.$row['LName'])
	->set('session',$hobo->session->toArray())
	->set('cookie',$hobo->cookie->toArray());

function raintpl($hobo, $params) {
	$hobo->rain->assign('URL',$hobo->get('URL'));
	$hobo->rain->assign('user',$hobo->get('user'));
	$hobo->rain->assign('title','Rain TPL test &middot; Hobo MVC');
	$hobo->rain->assign('heading','<a href="http://www.raintpl.com/">Rain TPL</a> test');
	$hobo->rain->assign('content','This page was loaded using Rain TPL template engine extension');
	$hobo->rain->assign('session',$hobo->session->toArray());
	$hobo->rain->assign('cookie',$hobo->cookie->toArray());
	$hobo->rain->draw('default');
}

function index($hobo, $params) {
	$hobo
		->set('title','HoboMVC')
		->set('heading','Hello World!')
		->set('content','This is Hello World index page using Hobo MVC Framework')
		->draw('default.php');
}

function markdown($hobo, $params) {
	$hobo
		->set('title','Markdown test &middot; Hobo MVC')
		->set('heading','Markdown test using <a href="http://www.parsedown.org/">Parsedown.php</a>')
		->set('content',$hobo->md->text(file_get_contents('README.md')))
		->draw('default.php');
}

function error($hobo, $params) {
	$hobo
		->set('title','Page not found &middot; Hobo MVC')
		->set('heading','404!')
		->set('content','This is not the web page you are looking for.')
		->draw('default.php');
}

$hobo->run();