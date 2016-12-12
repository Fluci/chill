<?php
namespace config;

$dynConfig = require_once DYNAMIC_CONFIG_DIR.'/config.php';
$debug = !$dynConfig['production'];

return array_merge($dynConfig, array(

'local' => array(
    'lang_code' => 'de'
),
'twig' => array(
    'properties' => array(
        'autoescape' => true,
        'cache' => TWIG_CACHE,
        'debug' => $debug,
        'strict_variables' => $debug,
// 'number_format' => array(2, '.', '\'') // TODO
    )
),
'debug' => $debug,
'error_reporting_level' => ($debug ? -1 : 0),
'use_mock' => $debug,
'timezone' => 'Europe/Zurich',
'timezone_datetime' => \DateTimeZone::EUROPE

));
