<?php

$reqCreator = new \Chill\Travel\RequestCreatorTimetable(
    $CONFIG['keys']['OPENTRANSPORTDATA_SWISS_API_KEY'],
    $CONFIG['timetable']['number_of_results']
);

$result;
if ($use_mock === false) {
    $reqTime = (time() + $CONFIG['timetable']['time_offset']);
    $options = $reqCreator->getContextOptions($stopPointRef, $reqTime);
    $context = stream_context_create($options);
    $result  = file_get_contents(
        $CONFIG['OPENTRANSPORTDATA_SWISS_API_URL'],
        false,
        $context
    );
} else {
    // Mock
    $result = file_get_contents(VER_RESOURCE_ROOT."/mock.txt");
}

if ($result === false) {
    return false;
}

$trias = simplexml_load_string($result);

$arrivals = $trias->ServiceDelivery->DeliveryPayload
    ->StopEventResponse->StopEventResult;

$journeys = array();

$journeyFactory = new \Chill\Travel\TravelFactorySimpleXml($CONFIG['timezone_datetime']);

foreach ($arrivals as $arrival) {
    $journeys[] = $journeyFactory->createJourney($arrival);
}

$timetable = array(
    // [ms]
    'refreshInterval' => $CONFIG['timetable']['refresh_interval'] * 1000
);


// ///// Print stuff
$template = $TWIG->loadTemplate('timetable.html.twig');
echo $template->render(
    array(
        'page' => $PAGE,
        'journeys' => $journeys,
        'timetable' => $timetable
    )
);
return true;
