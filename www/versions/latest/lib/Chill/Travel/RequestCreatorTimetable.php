<?php
/**
 * PHP version 5
 *
 * @category Travel
 * @package  Chill
 * @author   Felice Serena <felice@serena-mueller.ch>
 * @license  http://www.opensource.org/licenses/mit-license.html  MIT License
 */
namespace Chill\Travel;

/**
 * Formulates requests for
 * https://opentransportdata.swiss/de/cookbook/abfahrts-ankunftsanzeiger/
 */
class RequestCreatorTimetable extends RequestCreator
{
    private $numberOfResults = null;


    /**
     * Create a new object to create requests.
     * @param string  $apiKey          API key for opentransportdata.swiss
     * @param integer $numberOfResults How many results should be returned?
     */
    public function __construct($apiKey, $numberOfResults = 50)
    {
        parent::__construct($apiKey);
        $this->numberOfResults = $numberOfResults;
    }

    /**
     * How many results should be requested?
     * @return integer
     */
    public function getNumberOfResults()
    {
        return $this->numberOfResults;
    }

    /**
     * Sets number of results that is requested.
     * @param integer $numberOfResults [description]
     * @return self   For chaining.
     */
    public function setNumberOfResults($numberOfResults)
    {
        $this->numberOfResults = $numberOfResults;
        return $this;
    }

    /**
     * Creates the HTTP header as text representation.
     * @return string
     */
    public function getHeader()
    {
        $header = array(
                   "Content-type: text/XML",
                   "Authorization: ".$this->getApiKey(),
                  );

        return $header;
    }

    /**
     * Generates XML to be sent as request payload.
     * @param  string $stopPointRef Reference of observed station.
     * @param  [type] $depArrTime   Time at which we want to observe the station.
     *                              If null, now() is used.
     * @return string
     */
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

    /**
     * Generates the `options` array for an http request via `file_get_contents`.
     * @param  string $stopPointRef Reference of observed station.
     * @param  [type] $depArrTime   Time at which we want to observe the station.
     *                              If null, now() is used.
     * @return array
     */
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
