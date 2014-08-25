<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $this->title;?></title>

    <link rel="stylesheet" href="<?php echo URL;?>public/css/bootstrap.css">
	<link rel="stylesheet" href="<?php echo URL;?>public/css/style.css">
  </head>
  <body>
    <div class="container">
      <div class="header">
        <ul class="nav nav-pills pull-right">
          <li><a href="<?php echo URL;?>">Home</a></li>
          <li><a href="<?php echo URL;?>usage">Usage</a></li>
        </ul>
        <h3 class="text-muted"><?php echo $this->title;?></h3>
      </div>
	  <div class="jumbotron">
        <h1>Hobo <small>MVC Framework</small></h1>
        <p class="lead">Super simple and easy to use MVC framework. Perfect for quickly building real and clean applications.</p>
        <p><a class="btn btn-lg btn-success" href="https://github.com/deathbeam/hobomvc" role="button">Download now</a></p>
      </div>

      <div class="row marketing">
		<?php echo $this->content;?>
      </div>

      <div class="footer">
        <p>&copy; Thomas Slusny 2014</p>
      </div>

    </div> <!-- /container -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>>
	<script src="<?php echo URL;?>public/js/bootstrap.js"></script>
	<script src="<?php echo URL;?>public/js/github.commits.widget.js"></script>
	
	<script type='text/javascript'>
		$(document).ready(function() {
		  $('#github-commits').githubInfoWidget({
			user: 'deathbeam',
			repo: 'hobomvc',
			branch: 'master',
			last: 10,
			limitMessageTo: 70,
			avatarSize: 32
		  });
		  //Set the carousel options
		  $('#quote-carousel').carousel({
			pause: true,
			interval: 0,
		  });
		});
	</script>
  </body>
</html>