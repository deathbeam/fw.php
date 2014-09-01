<?php include 'header.php';?>
<div class="container">
	<h1><?php echo $heading;?></h1>
	<p>Database test: <code><?php echo $user;?></code></p>
	<p>Cookie test: <code><?php echo $cookie['test'];?></code></p>
	<p>Session test: <code><?php echo $session['test'];?></code></p>
</div>
<?php include 'footer.php';?>