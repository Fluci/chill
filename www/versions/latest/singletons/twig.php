<?php
require_once COMPOSER_ROOT.'/twig/twig/lib/Twig/Autoloader.php';

Twig_Autoloader::register();

return new Twig_Environment(
    new Twig_Loader_Filesystem(DIR_TEMPLATES), 
    $CONFIG['twig']['properties']
);
// $TWIG->getExtension('core')->setNumberFormat(2, '.', '\'');
// $TWIG->getExtension('core')->setTimezone('Europe/Paris');
// $TWIG->getExtension('core')->setDateFormat('d/m/Y', '%d days');
// $TWIG->addExtension(new Twig_Extension_Debug()); // for dump function
?>