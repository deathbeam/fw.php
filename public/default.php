<?php include 'header.php';?>
<div class="jumbotron">
	<h1><a href="<?php echo $URL ?>">less.php</a> <small><?php echo $heading;?></small></h1>
</div>
<div class="container">
	<?php echo $content;?>
</div>
<footer>
	<div class="container">
		<p class="text-muted"><?php echo 'Page rendered in '.round(1e3*(microtime(TRUE)-$TIME),2).' msecs / Memory usage '.round(memory_get_usage(TRUE)/1e3,1).' Kibytes'; ?></p>
	</div>
</footer>
<?php include 'footer.php';?>