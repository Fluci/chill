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
//	'jsIncludes' => array(),
//	'logo' => array('url' => '/b/l/logo.png', 'alg' => 'Logo'),
//	'jsFootSnippets' => array()
);
?>
