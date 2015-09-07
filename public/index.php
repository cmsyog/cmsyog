<?php

// PUBLIC_PATH
define('PUBLIC_PATH', __DIR__);

// bootstrap
require PUBLIC_PATH.'/../bootstrap.php';

$startTime = microtime(true);
define('BASEPATH',dirname(__FILE__) . '/../'  );


//
require_once('Core/YogApplication.php');
require_once('Core/functions.php');
require_once('Core/Model/BaseModel.php');
require_once('Core/Template/YogTemplate.php');
$app = new YogApplication();
//$app->startSession();
$app->execute();
