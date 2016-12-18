<?php
/**
 * PHP version 7
 *
 * @category Travel
 * @package  Chill
 * @author   Felice Serena <felice@serena-mueller.ch>
 * @license  http://www.opensource.org/licenses/mit-license.html  MIT License
 */
namespace Chill\Travel;

/**
 * Models a station halt (when, where, etc.).
 *
 * For original see
 * https://opentransportdata.swiss/de/cookbook/abfahrts-ankunftsanzeiger/
 *
 * @category Travel
 * @package  Chill
 */
class Journey
{
    private $previousCalls;
    private $thisCall;
    private $onwardCalls;
    private $service;


    /**
     * Creates a journey object that consists of the stations the vehicle passes
     * and a description of the service responsible for the journey.
     * @param array                  $previousCalls List of stations the
     *                                              vehicle already was.
     * @param Chill\Travel\StopPoint $thisCall      Observed station.
     * @param array                  $onwardCalls   List of stations the vehicle
     *                                              will be next.
     * @param Chill\Travel\Service   $service       Service description of journey.
     */
    public function __construct(
        array $previousCalls,
        \Chill\Travel\StopPoint $thisCall,
        array $onwardCalls,
        \Chill\Travel\Service $service
    ) {
        $this->previousCalls = $previousCalls;
        $this->thisCall      = $thisCall;
        $this->onwardCalls   = $onwardCalls;
        $this->service       = $service;
    }

    /**
     * List of stations the vehicle already passed.
     * @return array
     */
    public function getPreviousCalls()
    {
        return $this->previousCalls;
    }

    /**
     * Alias for `getPreviousCalls` to be compatible with the xml in twig.
     * @return array
     */
    public function getPreviousCall()
    {
        return $this->getPreviousCalls();
    }

    /**
     * The observed station.
     * @return Chill\Travel\StopPoint
     */
    public function getThisCall()
    {
        return $this->thisCall;
    }

    /**
     * List of stations the vehicle will pass after the observed station.
     * @return array
     */
    public function getOnwardCalls()
    {
        return $this->onwardCalls;
    }

    /**
     * Alias for `getOnwardCalls` to be compatible with the xml in twig.
     * @return array
     */
    public function getOnwardCall()
    {
        return $this->getOnwardCalls();
    }

    /**
     * Description of service responsible for journey.
     * @return \Chill\Travel\Service
     */
    public function getService()
    {
        return $this->service;
    }
}
