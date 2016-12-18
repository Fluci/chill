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
 * Models a station halt (when, where, etc.)
 *
 * @category Travel
 * @package  Chill
 */
class StopPoint
{
    private $ref       = -1;
    private $name      = null;
    private $arrival   = null;
    private $departure = null;
    private $seqNumber = -1;


    /**
     * Constructs a StopPoint initialized with all elements.
     * @param string                 $ref       Reference number.
     * @param string                 $name      Name of the station.
     * @param string                 $seqNumber If part of a journey,
     *                                          at which position is it placed?
     * @param \Chill\Travel\DateTime $arrival   [description]
     * @param \Chill\Travel\DateTime $departure [description]
     */
    public function __construct(
        $ref, $name, $seqNumber, $arrival = null, $departure = null
    ) {
        $this->ref       = $ref;
        $this->name      = $name;
        $this->arrival   = $arrival;
        $this->departure = $departure;
        $this->seqNumber = $seqNumber;
    }

    /**
     * Access to the reference number.
     * @return stirng Reference number.
     */
    public function getStopPointRef()
    {
        return $this->ref;
    }

    /**
     * Gives acces to name of stop point.
     * @return string Name of the stop point.
     */
    public function getStopPointName()
    {
        return $this->name;
    }

    /**
     * Returns when the vehicle should arrive at the stop point.
     * @return \Chill\Travel\DateTime Arrival time.
     */
    public function getServiceArrival()
    {
        return $this->arrival;
    }

    /**
     * Returns when the vehicle should leave the stop point.
     * @return \Chill\Travel\DateTime Departure tiem.
     */
    public function getServiceDeparture()
    {
        return $this->departure;
    }

    /**
     * Returns sequence number: position inside the journey of the vehicle.
     * @return string Sequence number.
     */
    public function getStopSeqNumber()
    {
        return $this->seqNumber;
    }
}
