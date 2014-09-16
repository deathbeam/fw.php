#{ fw.php } code less, create more
* [Hello World](#hello-world)
* [Installation](#installation)
* [Configuration](#configuration)
* [Templates](#templates)
* [Routes](#routes)
* [API](#api)
* [License](#license)

## Hello World
To create your first hello world fw.php application, create `index.php` and add below to it
```php
$fw->route('GET /@name',
    function($fw, $params) {
        echo'Hello, '.$params['name'].'!';
    }
);
```

## Installation
Copy this repo into a public accessible folder on your server (or to public_html folder of your FTP if you are using shared hosting). There are muliple ways to get fw.php:

a) download and extract .zip /.tgz by hand
```
https://github.com/deathbeam/fwphp/archive/master.zip
```

b) cloning the repo with git
```
git clone https://github.com/deathbeam/fwphp.git /your/public/web/folder
```

c) getting the repo via Composer
```
composer create-project deathbeam/fwphp /your/public/web/folder dev-master
```

Now, we need to install `mod_rewrite` becouse it is required for `.htaccess`.

## Configuration
fw.php can be configured in 2 ways. First one is using only `php` and second one is loading configuration from `json` file.
In examples below, we will:
* Load `cookie.php` extension from `plugins` folder
* Change directory of public files from default `public` to `new_public_dir`
* Set `/` route to `index` function

#### Using `php` only
This is basic configuration from `index.php`. 
```php
$fw->set('public_dir', 'new_public_dir');
$fw->cookie = 'cookie.php';
$fw->route('GET /', 'index');
```

#### Using `json` configuration file
Below is example on how to configure fw from .json file
```php
$fw->config('config.json');
```
And here is content of `config.json`
```JSON
{
	"globals": {
		"public_dir": "new_public_dir"
	},
	"libs": {
		"cookie": "cookie.php"
	},
	"routes": {
		"GET /": "index",
	}
}
```

## Templates
fw.php have super simple templating system using PHP as templating language.

Drawing template is simple:
```php
$fw->draw('test.php');
```
Templates can read global variables set by `$fw->set` method.

#### Example template
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
```html
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>The Default Template</title>
	</head>
	<body>
		<header>
			<h1>You are viewing the default page!</h1>
			<?=$header?>
		</header>
		<section>
			<?=$body?>
		</section>
		<footer>
			<?=$footer?>
		</footer>
	</body>
</html>
```
## Routes
In fw.php I implemented routing very similar to F3 routing. Features:
* Dynamic routing with named parameters
* Reversed routing
* Flexible regular expression routing
* ReST route mapping

#### Example routing
```php
// mapping routes
$fw->route('home: GET|POST /', 'home');
$fw->route('GET /users', 'users');
$fw->route('users_show: GET /users/@id', 'showUser');
$fw->route('users_do: POST /users/@id/@action', 'userController->@action');

// provide ReST interface by mapping HTTP requests to class method
$fw->route('MAP /rest', 'some_class');

// default route (404 page)
$fw->route('default', 'error');

// redirect
$fw->reroute('users_show', array('id' => 5));
$fw->reroute('/users');
```

## API
| Name                           | Usage                               | Description                                                     |
|--------------------------------|-------------------------------------|-----------------------------------------------------------------|
| __set(plugin, value)           | $fw->{plugin} = 'file'              | Loads specified plugin from plugin directory                    |
| __get(plugin)                  | $plug = $fw->{plugin}               | Gets specified already loaded plugin                            |
| set(key, value)                | $fw->set('key', 'value')            | Adds specified key to fw.ph stack                               |
| get(key)                       | $key = $fw->get('key')              | Gets specified key from fw.ph stack                             |
| exists(key)                    | $exists = $fw->exists('key')        | Checks if specified key exists                                  |
| clear(key)                     | $fw->clear('key')                   | Removes specified key from fw.php stack                         |
| stack()                        | $stack = $fw->stack()               | Publish stack contents                                          |
| hook(name, callable)           | $fw->hook('hook_name') = function() | Adds a new hook to fw.php                                       |
| invoke(hook, [opt] args)       | $fw->invoke('hook_name')            | Invokes specified hook                                          |
| config(file)                   | $fw->config('config.json')          | Configure fw.php from json configuration file                   |
| draw(template)                 | $fw->draw('template.php')           | Renders specified template                                      |
| route(pattern, callable)       | $fw->route('GET /', function())     | Adds route with specified pattern and callback to routing array |
| reroute(pattern, [opt] params) | $fw->reroute('/')                    | Redirects user to specified route                               |

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
