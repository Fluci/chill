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

$fetcher = new \Chill\Timetable\DataFetcher();

if ($use_mock === true) {
    $fetcher->setMockPath(VER_RESOURCE_ROOT."/mock.txt");
}

$cacher = new \Chill\Timetable\RequestCacher(
    VER_CACHE.'/timetable',
    $CONFIG['timetable']['cache_timeout']
);

$fetcher->setCacher($cacher);

$reqCreator = new \Chill\Timetable\RequestCreator(
    $CONFIG['OPENTRANSPORTDATA_SWISS_API_URL'],
    $CONFIG['keys']['OPENTRANSPORTDATA_SWISS_API_KEY'],
    $CONFIG['timetable']['number_of_results_fetch'],
    $CONFIG['timetable']['time_offset_fetch']
);

$fetcher->setRequestCreator($reqCreator);

$result = $fetcher->fetch($stopPointRef);

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

// Filter results to keep them fresh in case fetched from cache.
$min = time() + $CONFIG['timetable']['time_offset_show'];
$max = $CONFIG['timetable']['number_of_results_show'];

$fetchedJourneys = 0;

foreach ($arrivals as $arrival) {
    $journey = $journeyFactory->createJourney($arrival);
    $known   = $journey->getThisCall()->getServiceDeparture();


    if ($known === null) {
        $known = $journey->getThisCall()->getServiceArrival();
    }

    if ($known->getEstimatedTime()->getTimestamp() >= $min || $use_mock) {
        $journeys[] = $journey;
        if (++$fetchedJourneys >= $max) {
            break;
        }
    }
}

$observed = '';
if (empty($journeys) === false) {
    $observed = $journeys[0]->getThisCall()->getStopPointName()->getText();
}

$timetable = array(
    // [ms]
    'refreshInterval' => $CONFIG['timetable']['refresh_interval'] * 1000,
    'observedStation' => $observed,
    'stopPointRef' => $stopPointRef,
);

$template;
if ($data_only === true) {
    $template = 'timetable_data.html.twig';
} else {
    $template = 'timetable.html.twig';
}

// ///// Print stuff
$template = $TWIG->loadTemplate($template);
echo $template->render(
    array(
        'page' => $PAGE,
        'journeys' => $journeys,
        'timetable' => $timetable
    )
);
return true;
