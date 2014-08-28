<?php include $header;?>
<div class="container">
	<div class="main">
		<div style="text-align: left;">
			<?php echo $content;?>
		</div>
		<hr/>
		<p>Database test: <code><?php echo $user;?></code></p>
		<p>Cookie test: <code><?php echo $cookie['test'];?></code></p>
		<p>Session test: <code><?php echo $session['test'];?></code></p>
	</div>
</div>
<?php include $footer;?>