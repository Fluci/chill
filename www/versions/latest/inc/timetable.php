<?php
/**
 * Configures and coordinates display of the timetable.
 *
 * PHP version 5
 *
 * @category Config
 * @package  Chill
 * @author   Felice Serena <felice@serena-mueller.ch>
 * @license  http://www.opensource.org/licenses/mit-license.html  MIT License
 */

$result = false;
if ($use_mock === true) {
    // Mock
    $result = file_get_contents(VER_RESOURCE_ROOT."/mock.txt");
}

$cacher = new \Chill\Travel\TimetableRequestCacher(
    VER_CACHE.'/timetable',
    $CONFIG['timetable']['cache_timeout']
);

// Check cache.
if ($result === false) {
    $noResults = $CONFIG['timetable']['number_of_results'];
    $result    = $cacher->loadCached($stopPointRef, $noResults);
}

// No cache, need to request data.
if ($result === false) {
    $reqCreator = new \Chill\Travel\RequestCreatorTimetable(
        $CONFIG['keys']['OPENTRANSPORTDATA_SWISS_API_KEY'],
        $CONFIG['timetable']['number_of_results']
    );

    $reqTime = (time() + $CONFIG['timetable']['time_offset']);
}

// Try curl.
if ($result === false) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $CONFIG['OPENTRANSPORTDATA_SWISS_API_URL']);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $header = $reqCreator->getHeader();
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    $requestBody = $reqCreator->getRequestBody($stopPointRef, $reqTime);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);

    $result = curl_exec($ch);
    curl_close($ch);
    if ($result === false) {
        error_log("Curl could not fetch request for timetable.");
    } else {
        $noOfResults = $CONFIG['timetable']['number_of_results'];
        $cacher->store($stopPointRef, $noOfResults, $result);
    }
}

// Try file_get_contents.
if ($result === false) {
    if (ini_get('allow_url_fopen') !== "1") {
        error_log(
            'allow_url_fopen has value "'.ini_get('allow_url_fopen')
            .'. Must be "1". file_get_contents will probably fail.'
        );
    }

    $options = $reqCreator->getContextOptions($stopPointRef, $reqTime);
    $context = stream_context_create($options);
    $result  = file_get_contents(
        $CONFIG['OPENTRANSPORTDATA_SWISS_API_URL'],
        false,
        $context
    );
    if ($result === false) {
        error_log("file_get_contents could not fetch request for timetable.");
    } else {
        $cacher->store($stopPointRef, $noOfResults, $result);
    }
}

// Give up.
if ($result === false) {
    return false;
}

$trias = simplexml_load_string($result);

$arrivals = $trias->ServiceDelivery->DeliveryPayload
    ->StopEventResponse->StopEventResult;

$journeys = array();

$timezone       = new \DateTimeZone($CONFIG['timezone']);
$journeyFactory = new \Chill\Travel\TravelFactorySimpleXml($timezone);

foreach ($arrivals as $arrival) {
    $journeys[] = $journeyFactory->createJourney($arrival);
}

$observed = '';
if (empty($journeys) === false) {
    $observed = $journeys[0]->getThisCall()->getStopPointName()->getText();
}

$timetable = array(
    // [ms]
    'refreshInterval' => $CONFIG['timetable']['refresh_interval'] * 1000,
    'observedStation' => $observed,
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
