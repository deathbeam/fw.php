<?php
/**
 * Configuration for: Error reporting
 * Useful to show every little problem during development, but only show hard errors in production
 */
error_reporting(E_ALL);
ini_set("display_errors", 1);

/**
 * Configuration for: Project URL
 * Put URL to your project folder here, for local development "127.0.0.1" or "localhost" (plus sub-folder) is fine
 */
define('URL', 'http://127.0.0.1/hobomvc/');

/**
 * Configuration for: Database
 * This is the place where you define your database credentials, database type etc.
 */
define('USE_DB', false);
define('DB_TYPE', 'mysql');
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'hobomvc');
define('DB_USER', 'root');
define('DB_PASS', 'mysql');