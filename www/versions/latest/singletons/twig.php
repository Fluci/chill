<?php
/**
 * General twig configuration, ready to use.
 *
 * PHP version 5
 *
 * @category Config
 * @package  Chill
 * @author   Felice Serena <felice@serena-mueller.ch>
 * @license  http://www.opensource.org/licenses/mit-license.html  MIT License
 */

require_once VER_COMPOSER_ROOT.'/twig/twig/lib/Twig/Autoloader.php';

Twig_Autoloader::register();

return new Twig_Environment(
    new Twig_Loader_Filesystem(VER_TEMPLATES_ROOT),
    $CONFIG['twig']['properties']
);

/*
    $TWIG->getExtension('core')->setNumberFormat(2, '.', ''');
    $TWIG->getExtension('core')->setTimezone('Europe/Paris');
    $TWIG->getExtension('core')->setDateFormat('d/m/Y', '%d days');
    $TWIG->addExtension(new Twig_Extension_Debug()); // for dump function
*/
