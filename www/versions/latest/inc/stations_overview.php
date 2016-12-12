<?php

$stationsPath = RESOURCE_ROOT.'/bahnhof.csv';
$cachedPath = LOCAL_CACHE.'/stations.inc';
$cacheNew = $CONFIG['debug'];

if(!file_exists($stationsPath)) {
	return false;
}
if(!file_exists($cachedPath)) {
	$cacheNew = true;
}

if($cacheNew || filemtime($cachedPath) <= filemtime($stationsPath)) {
	$cacheNew = true;
} 

if($cacheNew){
$handle = fopen($stationsPath, "r");
if ($handle === FALSE) {
	return false;
}


// see https://opentransportdata.swiss/de/cookbook/bahnhofsliste/
$file = file($stationsPath);
//$row = fgetcsv($handle); // consume title row and ignore it
//$i = 0;


$stations = array();
//while (($row = fgetcsv($handle)) !== FALSE) {
foreach($file as $line) {
	$line = mb_convert_encoding($line, 'UTF-8');
	$row = str_getcsv($line);
	$refId = $row[0];


	$namesRaw = explode('$', $row[1]);
	$names = array();

	$lastName = array();
	foreach($namesRaw as $name){
		switch($name){
		case "<1>":
		case "<2>":
		case "<4>":
			$names[] = $lastName;
			break;
		case "<3>":
			// ignore
			break;
		default:
			$lastName = $name;
		}
	}
	foreach($names as $name) {
		$stations[] = array('stopPointRef' => $refId, 'stopPointName' => $name);
	}
}

usort($stations, function($a, $b){
	if($a['stopPointName'] === $b['stopPointName']) {
		return 0;
	}
	return $a['stopPointName'] < $b['stopPointName'] ? -1 : 1;
});

fclose($handle);

/////// print stuff

$template = $TWIG->loadTemplate('stations_overview.html.twig');
$output = $template->render(array(
	'page' => $PAGE, 
	'stations' => $stations
));
file_put_contents($cachedPath, $output);
echo $output;
return true;
}
include $cachedPath;
return true;
?>