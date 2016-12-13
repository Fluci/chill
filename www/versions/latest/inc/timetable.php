<?php
$url = 'https://api.opentransportdata.swiss/trias';

$req = array(
    'stopPointRef' => $stopPointRef,
    'timestamp' => \Chill\Util\Util::formatZulu(),
    'numberOfResults' => $CONFIG['timetable_number_of_results'],
    'depArrTime' => \Chill\Util\Util::formatZulu(time() + $CONFIG['timetable_time_offset'])
);

$br     = "\r\n";
$header = "Content-type: text/XML".$br
    ."Authorization: ".$CONFIG['keys']['OPENTRANSPORTDATA_SWISS_API_KEY'].$br;

$request = '<Trias version="1.1" '
        .'xmlns="http://www.vdv.de/trias" '
        .'xmlns:siri="http://www.siri.org.uk/siri" '
        .'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
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

// Use key 'http' even if you send the request to https://...
$options = array(
    'http' => array(
        'header'  => $header,
        'method'  => 'POST',
        'content' => $request
    )
);

$context = stream_context_create($options);
$result;
if ($use_mock === false) {
    $result = file_get_contents($url, false, $context);
} else {
    // Mock
    $result = file_get_contents(VER_RESOURCE_ROOT."/mock.txt");
}

if ($result === false) {
    return false;
}

$trias = simplexml_load_string($result);

$arrivals = $trias->ServiceDelivery->DeliveryPayload->StopEventResponse->StopEventResult;

$journeys = array();
$journeyFactory = new \Chill\Travel\TravelFactorySimpleXml($CONFIG['timezone_datetime']);
foreach ($arrivals as $j) {
    $journeys[] = $journeyFactory->createJourney($j);
}

// detailed description:
// https://opentransportdata.swiss/de/cookbook/abfahrts-ankunftsanzeiger/

$timetable = array(
    'refreshInterval' => 30*1000 // [ms]
);


/////// print stuff

$template = $TWIG->loadTemplate('timetable.html.twig');
echo $template->render(array(
    'page' => $PAGE,
    'journeys' => $journeys,
    'timetable' => $timetable
));
return true;
