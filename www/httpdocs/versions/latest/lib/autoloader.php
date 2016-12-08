<?php
require_once __DIR__.'/Chill/Util/Autoloader.php';

$loader = new \Chill\Util\Autoloader();
$loader->addNamespace("Chill", __DIR__.'/Chill');

spl_autoload_register($loader);
?>
