<?php
/**
 * Configuration file for the website.
 *
 * PHP version 5
 *
 * @category Config
 * @package  Chill
 * @author   Felice Serena <felice@serena-mueller.ch>
 * @license  http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace config;

$dynConfig = require_once STAT_CONFIG_ROOT.'/config.php';
$debug     = !$dynConfig['production'];

return array_merge(
    $dynConfig,
    array(
        'local' => array(
            'lang_code' => 'de'
        ),
        'twig' => array(
            'properties' => array(
                'autoescape' => true,
                'cache' => VER_TWIG_CACHE,
                'debug' => $debug,
                'strict_variables' => $debug,
            )
        ),
        'debug' => $debug,
        'error_reporting_level' => ($debug === true ? -1 : 0),
        'use_mock' => $debug,
        'timezone' => 'Europe/Zurich',
        'timetable' => array(
            'number_of_results' => 20,
            // Offset in timetable request
            // +10*60: only show stops after 10 min in future
            // -10*60: also show stops 10 minutes ago
            'time_offset' => 0,
            // Delay between two timetable requests before page reloads
            // on user side
            // [s]
            'refresh_interval' => 30,
            // How long is data from the cache valid?
            // [s]
            'cache_timeout' => 20
        ),
        'OPENTRANSPORTDATA_SWISS_API_URL' => 'https://api.opentransportdata.swiss/trias'
    )
);
