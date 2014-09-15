<?php
$fw->config('config.json');

function index($fw, $params) {
	$fw
		->set('title','Powered by fw.php')
		->set('heading','code less, create more')
		->set('content','
			Congratulations! Your fw.php application is running.
			<h3>Get Started</h3>
			<ol>
				<li>The application code is in <code>index.php</code></li>
				<li>The configuration code is in <code>config.json</code> (optional)</li>
				<li>Presentation logic is in <code>public</code> directory (can be changed via <code>public_dir</code> key)</li>
				<li>Plugins (optional) are in <code>plugin</code> directory (can be changed via <code>plugin_dir</code> key)</li>
				<li>Read the <a target="_blank" href="http://deathbeam.github.io/fwphp/docs.htm">online documentation</a></li>
				<li>Star and watch fw.php on <a target="_blank" href="https://github.com/deathbeam/fwphp">GitHub</a></li>
			</ol>
			<h3>Plugins</h3>
			fw.php is barebone but features very powerfull plugin system. As default, fw.php ships with these plugins:
			<ul>
				<li>cookie.php - OOP cookie management wrapper</li>
				<li>db.php - PDO MySQL database wrapper</li>
				<li>session.php - Simple OOP session management wrapper</li>
			</ul>
		')
		->draw('default.php');
}

function error($fw, $params) {
	$fw
		->set('title','Page not found &middot; fw.php')
		->set('heading','404!')
		->set('content','This is not the web page you are looking for.')
		->draw('default.php');
}