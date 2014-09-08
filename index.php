<?php
/** Step 1: Configure fw.php
 * Here we will configure fw using json 
 * configuration file.
 */
$fw->config('config.json');

/** Step 2: Define routes
 * In this step we will define functions
 * which we assigned to routes in step 2.
 */
function index($fw, $params) {
	$fw
		->set('title','fw.php')
		->draw('home.php');
}

function error($fw, $params) {
	$fw
		->set('title','Page not found &middot; fw.php')
		->set('heading','404!')
		->set('content','This is not the web page you are looking for.')
		->draw('default.php');
}

function docs($fw, $params) {
	$fw
		->set('title','documentation &middot; fw.php')
		->set('heading','documentation')
		->set('content',$fw->md->text(file_get_contents('README.md')))
		->draw('default.php');
}