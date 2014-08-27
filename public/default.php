<?php include $header;?>
<div class="container">
	<div class="main">
		<h1><?php echo $heading;?></h1>
        <p class="lead"><?php echo $content;?></p>
		<hr/>
		<p>Database test: <code><?php echo $user;?></code></p>
		<p>Cookie test: <code><?php echo $cookie['test'];?></code></p>
		<p>Session test: <code><?php echo $session['test'];?></code></p>
	</div>
</div>
<?php include $footer;?>