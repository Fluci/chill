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
    'stylesheets' => array(
        array(
            'url' => 'css/basic.css',
            'media' => 'all',
        ),
        array(
            'url' => 'css/screen.css',
            'media' => 'all',
        ),
    ),
    'jsIncludes' => array(
        'e/jquery-3.1.1.min.js',
        'e/jquery.csv.min.js',
    )
    // 'thisPage' => $_SERVER['REQUEST_URI'].'?'.$_SERVER['QUERY_STRING']
    // 'logo' => array('url' => '/b/l/logo.png', 'alg' => 'Logo'),
    // 'jsFootSnippets' => array()
);
