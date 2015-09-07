<?php
// BASE_PATH
define('BASE_PATH', __DIR__);

// VIEW_BASE_PATH
define('VIEW_BASE_PATH', BASE_PATH.'/app/views/');

// BASE_URL
$config = require BASE_PATH.'/config/config.php';
define('BASE_URL', $config['base_url']);

// TIME_ZONE
date_default_timezone_set($config['time_zone']);

// Autoload
require BASE_PATH.'/vendor/autoload.php';
set_include_path(
    BASE_PATH . PATH_SEPARATOR .
    get_include_path()
);

// whoops: php errors for cool kids
$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();