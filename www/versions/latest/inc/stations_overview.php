<?php

$stationsPath = VER_RESOURCE_ROOT.'/bahnhof.csv';
$cachedPath   = VER_CACHE.'/stations.inc';

if (file_exists($stationsPath) === false) {
    return false;
}


// See https://opentransportdata.swiss/de/cookbook/bahnhofsliste/
$reader   = new \Chill\Travel\BahnhofReader();
$stations = $reader->readFile($stationsPath);

// ///// Print stuff
$template = $TWIG->loadTemplate('stations_overview.html.twig');
$output   = $template->render(
    array(
        'page' => $PAGE,
        'stations' => $stations
    )
);
file_put_contents($cachedPath, $output);
echo $output;
return true;
