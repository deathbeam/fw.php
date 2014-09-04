<?php include 'header.php';?>
	<div class="jumbotron header">
	<div class="text-vertical-bottom">
		<h1><a href="<?php echo $URL ?>">less.php</a> <small>code less, create more</small></h1>
		<br/>
		<p>
			<a href="https://github.com/deathbeam/lessphp/archive/master.zip" class="btn btn-dark btn-lg"><i class="fa fa-download fa-fw"></i> Download</a>
			<a href="https://github.com/deathbeam/lessphp" class="btn btn-dark btn-lg"><i class="fa fa-github fa-fw"></i> Contribute</a>
			<a href="<?php echo $URL ?>docs" class="btn btn-dark btn-lg"><i class="fa fa-file-text fa-fw"></i> Documentation</a>
		</p>
		</div>
	</div>
<pre><code class="header container">$less = require('path/to/less.php');
$less->route('GET /',
	function() {
		echo 'Hello, world!';
	}
);
$less->run();</code></pre>
<?php include 'footer.php';?>