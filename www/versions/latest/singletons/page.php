<?php
/**
 * Environment passed to templates
 *
 */

return array(
    'langCode' => $CONFIG['local']['lang_code'],
    'stylesheets' => array(
        array('url' => 'css/basic.css', 'media' => 'all'),
        array('url' => 'css/screen.css', 'media' => 'all')
    )
    //'thisPage' => $_SERVER['REQUEST_URI'].'?'.$_SERVER['QUERY_STRING']
// 'jsIncludes' => array(),
// 'logo' => array('url' => '/b/l/logo.png', 'alg' => 'Logo'),
// 'jsFootSnippets' => array()
);
