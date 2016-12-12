<?php

require_once __DIR__.'/lib/autoloader.php';

require_once __DIR__.'/environment_paths.php';

// configuration of a website instance
$CONFIG = require_once VER_CONFIG_ROOT.'/config.php';
$PAGE = require_once VER_SINGLETONS_ROOT.'/page.php';
$TWIG = require_once VER_SINGLETONS_ROOT.'/twig.php';

error_reporting($CONFIG['error_reporting_level']);
date_default_timezone_set($CONFIG['timezone']);
header('content-type: text/html; charset=utf-8');
