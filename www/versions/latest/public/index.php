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

$use_mock = \Chill\Util\Util::methodRequest('mock', array('GET'), '0') === '1' ? true : $CONFIG['use_mock'];

// this id comes from
// https://opentransportdata.swiss/dataset/695c7af6-d486-4cde-9bf0-a92fdd581a4e/resource/911a4eb7-9d10-440f-904b-b0872b9727c1/download/bahnhof.csv
// https://opentransportdata.swiss/dataset/bhlist
$stopPointRef = \Chill\Util\Util::methodRequest('stopPointRef', array('GET'), '8503099');
$stopPointRef = intval($stopPointRef);

$PAGE['use_mock'] = $use_mock;

$url = 'https://api.opentransportdata.swiss/trias';

$req = array(
	'stopPointRef' => $stopPointRef,
	'timestamp' => \Chill\Util\Util::formatZulu(), // 2016-06-27T13:34:00
	'numberOfResults' => '10',
	'depArrTime' => \Chill\Util\Util::formatZulu(time()-10*60)
);

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
$result;
if (!$use_mock) {
	$result = file_get_contents($url, false, $context);
} else {
	// mock
	$result = file_get_contents("mock.txt");
}

if ($result === FALSE) { 
	echo "error, data not available.";
	die;
}

$trias = simplexml_load_string($result);

$arrivals = $trias->ServiceDelivery->DeliveryPayload->StopEventResponse->StopEventResult;

$journeys = array();
$journeyFactory = new \Chill\Travel\JourneyFactory($CONFIG['timezone_datetime']);
foreach($arrivals as $j) {
	$journeys[] = $journeyFactory->createJourneyFromResponseTree($j);
}

// detailed description: 
// https://opentransportdata.swiss/de/cookbook/abfahrts-ankunftsanzeiger/

/////// print stuff

$template = $TWIG->loadTemplate('timetable.html.twig');
echo $template->render(array('page' => $PAGE, 'journeys' => $journeys));

?>