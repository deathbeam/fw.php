<?php include 'header.php';?>
<div class="jumbotron header">
	<div class="text-vertical-bottom">
		<h1><a href="<?php echo $URL ?>" class="header-dark">{ fw<span>.php</span> }</a> <small class="hidden-xs">code less, create more</small></h1>
		<br/>
		<span class="hidden-xs">
			<a href="https://github.com/deathbeam/fwphp/archive/master.zip" class="btn btn-dark btn-lg"><i class="fa fa-download fa-fw"></i> Download</a>
			<a href="https://github.com/deathbeam/fwphp" class="btn btn-dark btn-lg"><i class="fa fa-github fa-fw"></i> Contribute</a>
			<a href="<?php echo $URL ?>docs" class="btn btn-dark btn-lg"><i class="fa fa-file-text fa-fw"></i> Documentation</a>
		</span>
		<span class="visible-xs">
			<a href="https://github.com/deathbeam/fwphp/archive/master.zip" class="btn btn-dark btn-md"><i class="fa fa-download fa-fw"></i> Download</a>
			<a href="https://github.com/deathbeam/fwphp" class="btn btn-dark btn-md"><i class="fa fa-github fa-fw"></i> Contribute</a>
			<a href="<?php echo $URL ?>docs" class="btn btn-dark btn-md"><i class="fa fa-file-text fa-fw"></i> Documentation</a>
		</span>
	</div>
</div>
<pre><code class="header container" style="padding-bottom: 0;">$fw = require('path/to/fw.php');
$fw->route('GET /[a:name]',
	function($fw, $params) {
		echo 'Hello, '.$params['name'].'!';
	}
);
$fw->run();</code></pre>
<?php include 'footer.php';?>