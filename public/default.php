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
		<h1><a href="<?=$url?>" class="header-dark">{ fw<span>.php</span> }</a> <small><?php echo $heading;?></small></h1>
	</header>
	<section class="container">
		<?php echo $content;?>
	</section>
	<footer class="container">
		<?=
			'Page rendered in '.round(1e3*(microtime(true)-$time),2).' msecs / 
			Memory usage '.round(memory_get_usage(TRUE)/1e3,1).' Kibytes';
		?>
	</footer>
	<a href="https://github.com/deathbeam/fwphp">
		<img style="position: absolute; top: 0; right: 0; border: 0;" src="img/github.png" alt="Fork me on GitHub">
	</a>
</body>
</html>