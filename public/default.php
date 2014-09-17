<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?=$title;?></title>
	<link rel="stylesheet" href="css/bootstrap.css">
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
	<header class="jumbotron">
		<h1>
			<a href="<?=$url?>" class="header-dark">{ fw<span>.php</span> }</a>
			<small><?=$heading?></small>
		</h1>
	</header>
	<section class="container">
		<?=$content?>
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
	</section>
	<footer class="container">
		<p>
			Powered by <?=$package?>
			version <?=$version?>
		</p>
		<p>
			Page rendered in <?=round(1e3*(microtime(true)-$time),2)?> msecs /
			Memory usage <?=round(memory_get_usage(TRUE)/1e3,1)?> Kibytes
		</p>
	</footer>
	<a href="https://github.com/deathbeam/fwphp">
		<img src="img/github.png" alt="Fork me on GitHub" style="position:absolute;top:0;right:0;border:0;">
	</a>
</body>
</html>