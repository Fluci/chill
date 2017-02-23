<?php
/**
 * Environment passed to templates.
 *
 * PHP version 5
 *
 * @category Config
 * @package  Chill
 * @author   Felice Serena <felice@serena-mueller.ch>
 * @license  http://www.opensource.org/licenses/mit-license.html  MIT License
 */

return array(
    'title' => 'chill',
    'langCode' => $CONFIG['local']['lang_code'],
    'lang' => array(
        'stations_overview' => 'Home',
        'repository' => 'Github',
        'about' => 'About',
    ),
    'stylesheets' => array(
        array('url' => 'e/bootstrap/dist/css/bootstrap.min.css'),
        array('url' => 'css/base.css'),
    ),
    'jsIncludes' => array(
        'js/base.js',
        'e/jquery/dist/jquery.min.js',
        'e/jquery-csv/src/jquery.csv.min.js',
        'e/bootstrap/dist/js/bootstrap.min.js',
    ),
    'nav_active' => null,
    'nav' => array(
        'stations_overview' => './',
        'repository' => 'https://github.com/Fluci/chill',
        'about' => './about.php'
    ),
    // 'thisPage' => $_SERVER['REQUEST_URI'].'?'.$_SERVER['QUERY_STRING']
    // 'logo' => array('url' => '/b/l/logo.png', 'alg' => 'Logo'),
    // 'jsFootSnippets' => array()
);
