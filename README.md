fw.php is aiming to be blazing fast and simple PHP micro framework. It is inspired by my personally favorite framework, F3(fat free), but
fw.php have only features what are barebone for framework. fw do not have caching, but it do not really needs it 
becouse fw is blazing fast with minimal overhead (fw.php have less than 10KB), its templating system uses only PHP
and is built on single PHP file which contains only 12 functions.

## Table of Contents
* [Installation](#installation)
* [A quickstart tutorial](#a-quickstart-tutorial)
* [Configuration](#configuration)
* [Views and Templates](#views-and-templates)
* [Routes](#routes)
* [License](#license)

## Installation

* copy this repo into a public accessible folder on your server (or to public_html folder of your FTP if you are using shared hosting).

Common techniques are a) downloading and extracting the .zip / .tgz by hand, b) cloning the repo with git (into var/www if you are on Linux or wamp/www if you are on Windows and have Wamp installed)
```
git clone https://github.com/deathbeam/fwphp.git /your/public/web/folder
```
or c) getting the repo via Composer
```
composer create-project deathbeam/fwphp /your/public/web/folder dev-master
```
* Now, we need to install `mod_rewrite` becouse it is required for `.htaccess`.

## A quickstart tutorial

To quickly create your first hello world application, here is minimalistic index.php example.
```php
$fw->route('GET /[:name]',
	function($fw, $params) {
		echo 'Hello, '.$params['name'].'!';
	}
);
```

## Configuration
fw.php can be configured in 2 ways. First one is defining globals and second one is loading them from config file.
In examples below, we will:
* Load `cookie.php` extension from `plugins/` folder
* Change directory of public files from default `public/` to `new_public_dir/`
* Set `/` route to `index` function

### Defining globals
This is basic configuration from index.php. 
```php
$fw->set('PUBLIC_DIR', 'new_public_dir/');
$fw->cookie = 'cookie.php';
$fw->route('GET /', 'index');
```

### Loading configuration file
Loading configuration file is as easy as drinking beer.
```php
$fw->config('config.json');
```
And some basic configuration example is below:
```JSON
{
	"globals": {
		"PUBLIC_DIR": "new_public_dir/"
	},
	"libs": {
		"cookie": "cookie.php"
	},
	"routes": {
		"GET /": "index",
	}
}
```

## Views and Templates
fw.php have super simple templating system using PHP as templating language.
Drawing template is simple:
```php
$fw->draw('test.php');
```
Templates can read global variables set by `$fw->set` method.

### Example template
Below, we will create simple template logic.
Code what will go into routed function in `index.php`
```php
$fw
	->set('header','This is example header')
	->set('body','Content goes here')
	->set('footer','This is example footer'))
	->draw('default.php');
```
We will save code below as `default.php` to `/public` directory
```php
<!doctype html>
	<head>
		<meta charset="utf-8">
		<title>The Default Template</title>
	</head>
	<body>
		<header>
			<h1>You are viewing the default page!</h1>
			<?php echo $header;?>
		</header>
		<section>
			<?php echo $body;?>
		</section>
		<footer>
			<?php echo $footer;?>
		</footer>
	</body>
</html>
```
## Routes
In fw.php we implemented very powerfull PHP router [AltoRouter](https://github.com/dannyvankooten/AltoRouter). Features
* Dynamic routing with named parameters
* Reversed routing
* Flexible regular expression routing
* Custom regexes

### Example routing
```php
// mapping routes
$fw->route('home: GET|POST /', 'home#index');
$fw->route('GET /users', array('c' => 'UserController', 'a' => 'ListAction'));
$fw->route('users_show: GET /users/[i:id]', 'users#show');
$fw->route('users_do: POST /users/[i:id]/[delete|update:action]', 'usersController#doAction');

// provide ReST interface by mapping HTTP requests to class method
$fw->route('/rest', 'some_random_class');

// default route (404 page)
$fw->route('default', 'error');

// redirect
$fw->reroute('users_show', array('id' => 5));
$fw->reroute('/users');
```
You can use the following limits on your named parameters. AltoRouter will create the correct regexes for you.
```
*                    // Match all request URIs
[i]                  // Match an integer
[i:id]               // Match an integer as 'id'
[a:action]           // Match alphanumeric characters as 'action'
[h:key]              // Match hexadecimal characters as 'key'
[:action]            // Match anything up to the next / or end of the URI as 'action'
[create|edit:action] // Match either 'create' or 'edit' as 'action'
[*]                  // Catch all (lazy, stops at the next trailing slash)
[*:trailing]         // Catch all as 'trailing' (lazy)
[**:trailing]        // Catch all (possessive - will match the rest of the URI)
.[:format]?          // Match an optional parameter 'format' - a / or . before the block is also optional
```
Some more complicated examples
```
@/(?[A-Za-z]{2}_[A-Za-z]{2})$ // custom regex, matches language codes like "en_us" etc.
/posts/[*:title][i:id]        // Matches "/posts/this-is-a-title-123"
/output.[xml|json:format]?    // Matches "/output", "output.xml", "output.json"
/[:controller]?/[:action]?    // Matches the typical /controller/action 
```
The character before the colon (the 'match type') is a shortcut for one of the following regular expressions
```php
'i'  => '[0-9]++'
'a'  => '[0-9A-Za-z]++'
'h'  => '[0-9A-Fa-f]++'
'*'  => '.+?'
'**' => '.++'
''   => '[^/\.]++'
```

## License
```
Copyright (c) 2014 Thomas Slusny

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
the Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
```