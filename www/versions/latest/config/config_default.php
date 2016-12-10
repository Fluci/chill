<?php
namespace config;

$debug = false;

return array(
'local' => array(
	'lang_code' => 'de'
),
'keys' => array(
	// https://opentransportdata.swiss/de/dev-dashboard/
	'OPENTRANSPORTDATA_SWISS_API_KEY' => ''
),
'twig' => array(
	'properties' => array(
		'autoescape' => true,
		'cache' => TWIG_CACHE,
		'debug' => $debug,
		'strict_variables' => $debug,
//		'number_format' => array(2, '.', '\'') // TODO
	)
),
'debug' => $debug,
'error_reporting_level' => ($debug ? -1 : 0),
'use_mock' => $debug,
'timezone' => 'Europe/Zurich',
'timezone_datetime' => \DateTimeZone::EUROPE
);

?>
