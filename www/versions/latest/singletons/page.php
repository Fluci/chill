<?php
/**
 * Environment passed to templates
 *
 * PHP version 7
 *
 * @author  Felice Serena <felice@serena-mueller.ch>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

return array(
    'langCode' => $CONFIG['local']['lang_code'],
    'lang' => array(
        'stations_overview' => 'Home',
        'repository' => 'Github',
        'about' => 'About',
    ),
    'stylesheets' => array(
        array('url' => 'e/b/css/bootstrap.min.css'),
        array('url' => 'css/base.css'),
    ),
    'jsIncludes' => array(
        'e/jquery-3.1.1.min.js',
        'e/jquery.csv.min.js',
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
