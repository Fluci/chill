<?php

define("WEBSERVER_ROOT", realpath(__DIR__.'/../..'));
define("DYNAMIC_DATA_DIR", WEBSERVER_ROOT.'/data');
define("DYNAMIC_LOGS_DIR", WEBSERVER_ROOT.'/logs');
define("DYNAMIC_TMP_DIR", WEBSERVER_ROOT.'/tmp');

define("COMPOSER_ROOT", __DIR__.'/vendor');
define("DIR_TEMPLATES", __DIR__.'/templates');
define("TWIG_CACHE", __DIR__.'/twig_cache');

// configuration of a website instance
$CONFIG = require_once __DIR__.'/config/config.php';
$PAGE = require_once __DIR__.'/singletons/page.php';
$TWIG = require_once __DIR__.'/singletons/twig.php';

error_reporting($CONFIG['error_reporting_level']);
date_default_timezone_set($CONFIG['timezone']);
header('content-type: text/html; charset=utf-8');

?>
