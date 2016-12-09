<?php
/**
 *
 * PHP version 5
 *
 * @category Website
 * @package  Chill
 * @author   Felice Serena <felice@serena-mueller.ch>
 * @license  MIT License
 */

require_once __DIR__.'/../environment.php';

$url = 'https://api.opentransportdata.swiss/trias';

function formatZulu($timestamp = null){
	if($timestamp == null){
		$timestamp = time();
	}
	return date('Y-m-d\TH:i:s\Z', $timestamp);
}

$req = array(
	'stopPointRef' => '8502113',
	'timestamp' => formatZulu(), // 2016-06-27T13:34:00
	'numberOfResults' => '10',
	'depArrTime' => formatZulu(time()-10*60)
);

//echo $req['depArrTime'];

$br = "\r\n";
$header = "Content-type: text/XML".$br
	."Authorization: ".$CONFIG['keys']['OPENTRANSPORTDATA_SWISS_API_KEY'].$br;

$request = '<Trias version="1.1" xmlns="http://www.vdv.de/trias" xmlns:siri="http://www.siri.org.uk/siri" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
    .'<ServiceRequest>'
        .'<siri:RequestTimestamp>'.$req['timestamp'].'</siri:RequestTimestamp>'
        .'<siri:RequestorRef>EPSa</siri:RequestorRef>'
        .'<RequestPayload>'
            .'<StopEventRequest>'
                .'<Location>'
                    .'<LocationRef>'
                        .'<StopPointRef>'.$req['stopPointRef'].'</StopPointRef>'
                    .'</LocationRef>'
                    .'<DepArrTime>'.$req['depArrTime'].'</DepArrTime>'
                .'</Location>'
                .'<Params>'
                    .'<NumberOfResults>'.$req['numberOfResults'].'</NumberOfResults>'
                    .'<StopEventType>arrival</StopEventType>'
                    .'<IncludePreviousCalls>true</IncludePreviousCalls>'
                    .'<IncludeOnwardCalls>true</IncludeOnwardCalls>'
                    .'<IncludeRealtimeData>true</IncludeRealtimeData>'
                .'</Params>'
            .'</StopEventRequest>'
        .'</RequestPayload>'
    .'</ServiceRequest>'
.'</Trias>';

// use key 'http' even if you send the request to https://...
$options = array(
    'http' => array(
        'header'  => $header,
        'method'  => 'POST',
        'content' => $request
    )
);
$context  = stream_context_create($options);

// $result = file_get_contents($url, false, $context);
// mock
$result = file_get_contents("mock.txt");

if ($result === FALSE) { 
	echo "error!!";
	die;
}
$trias = simplexml_load_string($result);
$arrivals = $trias->ServiceDelivery->DeliveryPayload->StopEventResponse->StopEventResult;
//echo '<pre>';
//echo htmlentities(str_replace('><', ">\n<", $request));
/*
foreach($arrivals as $a) {
	print_r($a);
}
//*/
/*
$TIMETABLE = array();
foreach($arrivals as $a) {
	$LINE = array();
	$start = $a->StopEvent->PreviousCall[0]->CallAtStop;
	$stop = $a->StopEvent->ThisCall->CallAtStop;
	$b = $a->StopEvent->OnwardCall;
	$end = $b[count($b-1)]->CallAtStop;

	$oc = $a->StopEvent->xpath("*[last()]");
	print_r($oc);

	$estimated = $stop->ServiceDeparture->EstimatedTime;
	if($estimated != null) {
		$estimated = ', '.$estimated;
	} else {
		$estimated = "";
	}
	$rows[] = 
		$start->StopPointName->Text . " (" . $start->ServiceDeparture->TimetabledTime . ") -> " . 
		$stop->StopPointName->Text . ': '.$stop-> ServiceArrival->TimetabledTime .$estimated. ' -> ' .
		$end->StopPointName->Text . ' (' . $end->ServiceArrival->TimetabledTime . ')'
		."\n";
	$arr = array();
	$arr['FirstCall'] = $start;
	$arr['ThisCall'] = $start;
	$arr['startPoint'] = $start;
	$arrivalRows[] = $arr;
}
//*/

// detailed description: 
// https://opentransportdata.swiss/de/cookbook/abfahrts-ankunftsanzeiger/
$TIMETABLE = array();
$TIMETABLE['trias'] = $trias;

/////// print stuff

$template = $TWIG->loadTemplate('timetable.html.twig');
echo $template->render(array('page' => $PAGE, 'trias' => $trias));

?>