<?php

$stationsPath = RESOURCE_ROOT.'/bahnhof.csv';
$cachedPath = LOCAL_CACHE.'/stations.inc';

if (!file_exists($stationsPath)) {
    return false;
}


// see https://opentransportdata.swiss/de/cookbook/bahnhofsliste/
$reader = new \Chill\Util\BahnhofReader();
$stations = $reader->readFile($stationsPath);

/////// print stuff
$template = $TWIG->loadTemplate('stations_overview.html.twig');
$output = $template->render(array(
    'page' => $PAGE,
    'stations' => $stations
));
file_put_contents($cachedPath, $output);
echo $output;
return true;
