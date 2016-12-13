<?php
/**
 * Formulates xml requests for opentransportdata.swiss.
 *
 * PHP version 7
 *
 * @category Util
 * @package  Chill
 * @author   Felice Serena <felice@serena-mueller.ch>
 * @license  MIT License
 */

namespace Chill\Travel;

/**
 * Creates requests for
 * https://opentransportdata.swiss/de/cookbook/abfahrts-ankunftsanzeiger/
 */
class RequestCreatorTimetable extends RequestCreator
{
    private $numberOfResults = null;


    public function __construct($apiKey, $numberOfResults = 50)
    {
        parent::__construct($apiKey);
        $this->numberOfResults = $numberOfResults;
    }

    public function getNumberOfResults()
    {
        return $this->numberOfResults;
    }

    public function setNumberOfResults($numberOfResults)
    {
        $this->numberOfResults = $numberOfResults;
        return $this;
    }

    public function getHeader()
    {
        $header = array(
                   "Content-type: text/XML",
                   "Authorization: ".$this->getApiKey(),
                  );

        return $header;
    }

    public function getRequestBody($stopPointRef, $depArrTime = null)
    {
        $now         = \Chill\Util\Util::formatZulu();
        $depArrTimeF = \Chill\Util\Util::formatZulu($depArrTime);

        $request = '<Trias version="1.1" '
                .'xmlns="http://www.vdv.de/trias" '
                .'xmlns:siri="http://www.siri.org.uk/siri" '
                .'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            .'<ServiceRequest>'
                .'<siri:RequestTimestamp>'.$now.'</siri:RequestTimestamp>'
                .'<siri:RequestorRef>EPSa</siri:RequestorRef>'
                .'<RequestPayload>'
                    .'<StopEventRequest>'
                        .'<Location>'
                            .'<LocationRef>'
                                .'<StopPointRef>'.$stopPointRef.'</StopPointRef>'
                            .'</LocationRef>'
                            .'<DepArrTime>'.$depArrTimeF.'</DepArrTime>'
                        .'</Location>'
                        .'<Params>'
                            .'<NumberOfResults>'
                                .$this->getNumberOfResults()
                            .'</NumberOfResults>'
                            .'<StopEventType>arrival</StopEventType>'
                            .'<IncludePreviousCalls>true</IncludePreviousCalls>'
                            .'<IncludeOnwardCalls>true</IncludeOnwardCalls>'
                            .'<IncludeRealtimeData>true</IncludeRealtimeData>'
                        .'</Params>'
                    .'</StopEventRequest>'
                .'</RequestPayload>'
            .'</ServiceRequest>'
        .'</Trias>';

        return $request;
    }

    public function getContextOptions($stopPointRef, $depArrTime = null)
    {

        $header  = implode("\r\n", $this->getHeader())."\r\n";
        $request = $this->getRequestBody($stopPointRef, $depArrTime);

        // Use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => $header,
                'method'  => 'POST',
                'content' => $request
            )
        );

        return $options;
    }
}
