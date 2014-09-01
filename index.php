<?php
$hobo = require('application/base.php');
$hobo->config('config.json')->apply();

function index($hobo, $params) {
	$hobo
		->set('title','HoboMVC')
		->set('heading','Hello World!')
		->set('content','This is Hello World index page using Hobo MVC Framework')
		->draw('default.php');
}

function error($hobo, $params) {
	$hobo
		->set('title','Page not found &middot; Hobo MVC')
		->set('heading','404!')
		->set('content','This is not the web page you are looking for.')
		->draw('default.php');
}

function database($hobo, $params) {
	$row = $hobo->db
		->query('SELECT FName, LName, Age, Gender FROM mytable WHERE LName = :lname')
		->bind(':lname', 'Slusny')
		->fetchRow();
	$hobo->session->start();
	$hobo->session->set('test','This is session var');
	$hobo->cookie->set('test','This is cookie');

	$hobo
		->set('user',$row['FName'].' '.$row['LName'])
		->set('session',$hobo->session->toArray())
		->set('cookie',$hobo->cookie->toArray())
		->set('title','Database test &middot; Hobo MVC')
		->set('heading','Database, session and cookie extension test')
		->draw('tests.php');
}

function markdown($hobo, $params) {
	$hobo
		->set('title','Markdown test &middot; Hobo MVC')
		->set('heading','Markdown test using <a href="http://www.parsedown.org/">Parsedown</a>')
		->set('content',$hobo->md->text(file_get_contents('README.md')))
		->draw('default.php');
}

function raintpl($hobo, $params) {
	$hobo->rain->assign('URL',$hobo->get('URL'));
	$hobo->rain->assign('title','Rain TPL test &middot; Hobo MVC');
	$hobo->rain->assign('heading','Templating test using <a href="http://www.raintpl.com/">Rain TPL</a>');
	$hobo->rain->assign('content','This page was loaded using Rain TPL template engine extension');
	$hobo->rain->draw('default');
}

$hobo->run();