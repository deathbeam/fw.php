# Hobo MVC
Hobo MVC is aiming to be super simple and super intuitive MVC framework. It is inspired by 2 my personally favorite MVC frameworks, F3(fat free) framework and PHP-MVC framework, but
Hobo do not have that many features as both of them. Hobo core is currently built on 2 libs, and they are `router.php`,
and `base.php`. Names of these libs are self-explaining. It is easily extensible (for example check `/libs` folder).

## Table of Contents
* [Installation](#installation)
* [A quickstart tutorial](#a-quickstart-tutorial)
* [Routes](#routes)
* [Configuration](#configuration)
* [License](#license)

## Installation

### Using GitHub
First, copy this repo into a public accessible folder on your server (or to public_html folder of your FTP if you are using shared hosting).
Common techniques are a) downloading and extracting the .zip / .tgz by hand, b) cloning the repo with git (into var/www if you are on Linux or wamp/www if you are on Windows and have Wamp installed)
```
git clone https://github.com/deathbeam/hobomvc.git /your/public/web/folder
```

### Getting the repo via Composer
```
composer create-project deathbeam/hobomvc /your/public/web/folder dev-master
```

## A quickstart tutorial

To quickly create your first hello world application in Hobo, here is minimalistic index.php example.

Include Hobo base at start of your index.php file, right after `<?php` PHP opening tag.
```php
$hobo = require("application/base.php");
$hobo->route("GET /", "index");
$hobo->route("default", "error");

function index() {
	echo "Hello world.";
}
function error() {
	echo "404! This is not the web page you are looking for.";
}

$hobo->run();
```

## Routes
In Hobo we implemented very powerfull PHP router [AltoRouter](https://github.com/dannyvankooten/AltoRouter). Features
* Dynamic routing with named parameters
* Reversed routing
* Flexible regular expression routing
* Custom regexes

### Example routing
```php
// mapping routes
$hobo->route('GET|POST @home /', 'home#index');
$hobo->route('GET /users', array('c' => 'UserController', 'a' => 'ListAction'));
$hobo->route('GET @users_show /users/[i:id]', 'users#show');
$hobo->route('POST @users_do /users/[i:id]/[delete|update:action]', 'usersController#doAction');

// reversed routing
$hobo->generate('users_show', array('id' => 5));
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

## Configuration
Hobo can be configured in 2 ways. First one is defining globals and second one is loading them from config file.
In examples below, we will:
* Load database class from `libs/` folder
* Change directory of public files from default `public/` to `new_public_dir/`
* Set `/` route to `index` function

### Defining globals
This is basic configuration from index.php. 
```php
$hobo->db = 'db.php';
$hobo->set('PUBLIC_DIR', 'new_public_dir/');
$hobo->apply();
$hobo->route('GET /', 'index');
```

### Loading configuration file
Loading configuration file is as easy as drinking beer.
```php
$hobo->config('config.json');
$hobo->apply();
```
And some basic configuration example is below:
```JSON
{
	"globals": {
		"PUBLIC_DIR": "new_public_dir/"
	},
	"libs": {
		"db": "db.php"
	},
	"routes": {
		"GET /": "index",
	}
}
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
