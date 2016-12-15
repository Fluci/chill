<?php
/**
 * Create cached stations.csv and deilver page.
 *
 * PHP version 7
 *
 * @author  Felice Serena <felice@serena-mueller.ch>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

$stationsPath = VER_RESOURCE_ROOT.'/bahnhof.csv';
$cachedPath   = VER_RES_CACHE.'/stations.csv';

if (file_exists($stationsPath) === false) {
    return false;
}

if (file_exists(VER_RES_CACHE) === false) {
    mkdir(VER_RES_CACHE, 077, true);
}

$fExists = file_exists($cachedPath);

// Write cache.
if ($fExists === false || filemtime($cachedPath) <= filemtime($stationsPath)) {
    // See https://opentransportdata.swiss/de/cookbook/bahnhofsliste/
    $reader   = new \Chill\Travel\BahnhofReader();
    $stations = $reader->readFile($stationsPath);

    $cacheHandle = fopen($cachedPath, 'w');
    fwrite($cacheHandle, "StationID,Station\r\n");

    foreach ($stations as $stat) {
        $s = array(
              $stat['stopPointRef'],
              $stat['stopPointName'],
             );
        fputcsv($cacheHandle, $s);
    }

    fclose($cacheHandle);
}

$stopPointRef = \Chill\Util\Util::methodRequest('stop_point_ref', array('GET'), '-1');
$stopPointSearchText = \Chill\Util\Util::methodRequest('stop_point_search_text', array('GET'), '');

// ///// Print stuff
$PAGE['stopPointRef']        = $stopPointRef;
$PAGE['stopPointSearchText'] = $stopPointSearchText;
$PAGE['jsIncludes'][] = 'js/stations_overview.js';
$PAGE['nav_active']   = 'stations_overview';

$template = $TWIG->loadTemplate('stations_overview.html.twig');
$output   = $template->render(
    array(
        'page' => $PAGE
    )
);

echo $output;
return true;
