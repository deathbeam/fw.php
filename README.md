# Hobo MVC

Hobo MVC is aiming to be super simple and super intuitive MVC framework. It is inspired by F3(fat free) framework, but
Hobo do not have that many extravagant nonsense as F3. Hobo core is currently built on 3 libs, and they are `router.php`,
`session.php` and `view.php`. Names of these libs are self-explaining.

## Installation

### Using GitHub

First, copy this repo into a public accessible folder on your server (or to public_html folder of your FTP if you are using shared hosting).
Common techniques are a) downloading and extracting the .zip / .tgz by hand, b) cloning the repo with git (into var/www if you are on Linux or wamp/www if you are on Windows and have Wamp installed)

```
git clone https://github.com/panique/php-mvc.git /your/public/web/folder
```

### Getting the repo via Composer

```
composer create-project deathbeam/hobomvc /your/public/web/folder dev-master
```

1. mod_rewrite is required for Hobo routing. Most of web hosts have it already installed, but if you are hosting Hobo by self, you need to install it.

2. Change the .htaccess file from
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

4. Edit the *application/config/config.php*, change this line
```php
define('URL', 'http://127.0.0.1/hobomvc/');
```
to where your project is. Real domain, IP or 127.0.0.1 when developing locally. Make sure you put the sub-folder
in here (when installing in a sub-folder) too, also don't forget the trailing slash !

5. Edit the *application/config/config.php*, change these lines
```php
define('USE_DB', false)
define('DB_TYPE', 'mysql');
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'php-mvc');
define('DB_USER', 'root');
define('DB_PASS', 'mysql');
```
to your database credentials. Also, if you wanna use database, change USE_DB to true. Only change the type `mysql` if you
know what you are doing.

## A quickstart tutorial

To quickly create your first hello world application in Hobo, here is step by step tutorial.

1. Include Hobo base at start of your index.php file, right after `<?php` PHP opening tag.
```
$hobo = require("application/base.php");
```
2. Now we will create 2 functions what will echo simple hello world messages when called.
```
function index() {
	echo "Hello world.";
}
function error() {
	echo "404! This is not the web page you are looking for.;
}
```
3. And now it is time to link these 2 functions to their URL routes. We will set error function to be default route
because default route is called when we will type relative URL what is not defined in routing table.
```
$hobo->route("/", "index");
$hobo->default_route("error");
```
4. And last line in file should always be this command what will execute Hobo router, so we will add it after all above code.
```
$hobo->run();
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