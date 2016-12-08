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
/*$result = file_get_contents($url, false, $context);
if ($result === FALSE) { 
	echo "error!!";
	die;
}
*/

$result = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<Trias xmlns=\"http://www.vdv.de/trias\" version=\"1.1\">
    <ServiceDelivery>
        <ResponseTimestamp xmlns=\"http://www.siri.org.uk/siri\">2016-08-18T08:10:51Z</ResponseTimestamp>
        <Status xmlns=\"http://www.siri.org.uk/siri\">true</Status>
        <MoreData>false</MoreData>
        <Language>de</Language>
        <DeliveryPayload>
            <StopEventResponse>
                <StopEventResult>
                    <ResultId>ID-01C48EFC-863C-41B1-9841-43C85D0DDD58</ResultId>
                    <StopEvent>

<PreviousCall>
    <CallAtStop>
        <StopPointRef>8500028</StopPointRef>
        <StopPointName>
            <Text>Tecknau</Text>
            <Language>DE</Language>
        </StopPointName>
        <ServiceArrival>
            <TimetabledTime>2016-08-18T02:54:00Z</TimetabledTime>
        </ServiceArrival>
        <ServiceDeparture>
            <TimetabledTime>2016-08-18T02:54:00Z</TimetabledTime>
        </ServiceDeparture>
        <StopSeqNumber>1</StopSeqNumber>
    </CallAtStop>
</PreviousCall>

<ThisCall>
    <CallAtStop>
        <StopPointRef>8502113</StopPointRef>
        <StopPointName>
            <Text>Aarau</Text>
            <Language>DE</Language>
        </StopPointName>
        <ServiceDeparture>
            <TimetabledTime>2016-11-10T09:24:00Z</TimetabledTime>
            <EstimatedTime>2016-11-10T09:24:00Z</EstimatedTime>
        </ServiceDeparture>
        <StopSeqNumber>2</StopSeqNumber>
    </CallAtStop>
</ThisCall>

<OnwardCall>
    <CallAtStop>
        <StopPointRef>8502192</StopPointRef>
        <StopPointName>
            <Text>Distelberg</Text>
            <Language>DE</Language>
        </StopPointName>
        <ServiceArrival>
            <TimetabledTime>2016-11-10T09:27:00Z</TimetabledTime>
        </ServiceArrival>
        <ServiceDeparture>
            <TimetabledTime>2016-11-10T09:27:00Z</TimetabledTime>
        </ServiceDeparture>
        <StopSeqNumber>4</StopSeqNumber>
    </CallAtStop>
</OnwardCall>
                    </StopEvent>
                </StopEventResult>
            </StopEventResponse>
        </DeliveryPayload>
    </ServiceDelivery>
</Trias>";

$trias = simplexml_load_string($result);
$arrivals = $trias->ServiceDelivery->DeliveryPayload->StopEventResponse->StopEventResult;
//echo '<pre>';
//echo htmlentities(str_replace('><', ">\n<", $request));
/*
foreach($arrivals as $a) {
	print_r($a);
}
//*/

$rows = array();

foreach($arrivals as $a) {
	$start = $a->StopEvent->PreviousCall[0]->CallAtStop;
	$stop = $a->StopEvent->ThisCall->CallAtStop;
	$end = $a->StopEvent->OnwardCall->CallAtStop;
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

// detailed description: 
// https://opentransportdata.swiss/de/cookbook/abfahrts-ankunftsanzeiger/
$TIMETABLE = array();
$TIMETABLE['trias'] = $trias;
$TIMETABLE['arrivals'] = $arrivalRows;
$TIMETABLE['rows'] = $rows;

/////// print stuff

$template = $TWIG->loadTemplate('timetable.html.twig');
echo $template->render(array('page' => $PAGE, 'timetable' => $TIMETABLE));

?>