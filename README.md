# Hobo MVC

Hobo MVC is aiming to be super simple and super intuitive MVC framework. It is inspired by 2 my personally favorite MVC frameworks, F3(fat free) framework and PHP-MVC framework, but
Hobo do not have that many features as both of them. Hobo core is currently built on 2 libs, and they are `router.php`,
and `base.php`. Names of these libs are self-explaining. It is easily extensible (for example check `/libs` folder).

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

### Configure .htaccess
**mod_rewrite** is required for Hobo routing. Most of web hosts have it already installed, but if you are hosting Hobo by self, you need to install it.

Change the .htaccess file from
```
RewriteBase /hobomvc/
```
to where you put this project, relative to the web root folder. So when you put this project into
the web root, like directly in /var/www or /wamp/www, then the line should look like or can be commented out:
```
RewriteBase /
```
If you have put the project into a sub-folder, then put the name of the sub-folder here:
```
RewriteBase /sub-folder/
```

## A quickstart tutorial

To quickly create your first hello world application in Hobo, here is step by step tutorial.

Include Hobo base at start of your index.php file, right after `<?php` PHP opening tag.
```php
$hobo = require("application/base.php");
```
If your Hobo installation is in sub-folder and not in root directory, we need to set URL
```php
$hobo->set('URL', 'http://127.0.0.1/hobomvc/');
```
If you set URL (or any other setting for some of extension libs), we must apply it, so place this right after global setting definitions. If you do not defined any settings, this line is not required.
```php
$hobo->apply();
```
Now we will create 2 functions what will echo simple hello world messages when called.
```php
function index() {
	echo "Hello world.";
}
function error() {
	echo "404! This is not the web page you are looking for.";
}
```
And now it is time to link these 2 functions to their URL routes. We will set error function to be default route
because default route is called when we will type relative URL what is not defined in routing table.
```php
$hobo->route("GET /", "index");
$hobo->route("default", "error");
```
And last line in file should always be this command what will execute Hobo router, so we will add it after all above code.
```php
$hobo->run();
```

## Configuration
Hobo can be configured in 2 ways. First one is defining globals and second one is loading them from config file.
Add these functions right after `$hobo = require("application/base.php");`

### Defining globals
This is basic configuration using `$hobo=>set` function. 
```php
$hobo->set('URL', 'http://127.0.0.1/hobomvc/');
$hobo->apply();
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
		"URL": "http://127.0.0.1/hobomvc/"
	},
	"libs": {
		"session": "session.php",
		"cookie": "cookie.php",
		"db": "db.php",
		"view": "view.php"
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
