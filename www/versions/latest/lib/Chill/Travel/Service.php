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
 * Models a service: https://opentransportdata.swiss/de/cookbook/service-vdv-431/
 *
 * @category Travel
 * @package  Chill
 */
class Service
{
    private $operatingDayRef;
    private $vehicleRef;
    private $journeyRef;
    private $lineRef;
    private $directionRef;
    private $mode;
    private $publishedLineName;
    private $operatorRef;
    private $routeDescription;
    private $vias;
    private $originStopPointRef;
    private $originText;
    private $destStopPointRef;
    private $destText;


    /**
     * [__construct description]
     * @param \DateTime          $operatingDayRef    Day of operation.
     * @param string             $vehicleRef         Vehicle reference.
     * @param string             $journeyRef         Journey reference.
     * @param string             $lineRef            Line reference.
     * @param string             $directionRef       Direction id: outwards, return
     * @param \Chill\Travel\Mode $mode               Type of vehicle used.
     * @param \Chill\Util\Text   $publishedLineName  Name of service in public.
     * @param string             $operatorRef        Id of operator of line.
     * @param string             $routeDescription   Description of route.
     * @param string             $vias               Important stops on journey.
     * @param string             $originStopPointRef Where the journey started.
     * @param \Chill\Util\Text   $originText         Text description of start.
     * @param string             $destStopPointRef   Where the journey ends.
     * @param \Chill\Util\Text   $destText           Text description of last stop.
     */
    public function __construct(
        \DateTime $operatingDayRef,
        $vehicleRef,
        $journeyRef,
        $lineRef,
        $directionRef,
        \Chill\Travel\Mode $mode,
        \Chill\Util\Text $publishedLineName,
        $operatorRef = null,
        $routeDescription = null,
        $vias = null,
        $originStopPointRef = null,
        \Chill\Util\Text $originText = null,
        $destStopPointRef = null,
        \Chill\Util\Text $destText = null
    ) {
        if (is_string($journeyRef) === false) {
            trigger_error("journeyRef must be string", E_ERROR);
        }

        if (is_string($lineRef) === false) {
            trigger_error("lineRef must be string", E_ERROR);
        }

        if (is_string($directionRef) === false) {
            trigger_error("directionRef must be string", E_ERROR);
        }

        if (is_string($operatorRef) === false) {
            trigger_error("operatorRef must be string", E_ERROR);
        }

        $this->operatingDayRef = $operatingDayRef;
        $this->vehicleRef      = $vehicleRef;
        $this->journeyRef      = $journeyRef;
        $this->lineRef         = $lineRef;
        $this->directionRef    = $directionRef;
        $this->mode            = $mode;
        $this->publishedLineName = $publishedLineName;
        $this->operatorRef       = $operatorRef;
        $this->routeDescription  = $routeDescription;

        $this->vias = $vias;
        $this->originStopPointRef = $originStopPointRef;
        $this->originText         = $originText;
        $this->destStopPointRef   = $destStopPointRef;
        $this->destText           = $destText;
    }

    /**
     * Returns the date when this journey takes place.
     * @return \DateTime
     */
    public function getOperatingDayRef()
    {
        return $this->operatingDayRef;
    }

    /**
     * Vehicle reference.
     * @return string
     */
    public function getVehicleRef()
    {
        return $this->vehicleRef;
    }

    /**
     * Journey reference.
     * @return string
     */
    public function getJourneyRef()
    {
        return $this->journeyRef;
    }

    /**
     * Line reference.
     * @return string
     */
    public function getLineRef()
    {
        return $this->lineRef;
    }

    /**
     * Direction reference.
     * @return string
     */
    public function getDirectionRef()
    {
        return $this->directionRef;
    }

    /**
     * Type of vehicle.
     * @return \Chill\Travel\Mode
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Public name of line.
     * @return \Chill\Util\Text
     */
    public function getPublishedLineName()
    {
        return $this->publishedLineName;
    }

    /**
     * Reference of entity that is responsible.
     * @return string
     */
    public function getOperatorRef()
    {
        return $this->operatorRef;
    }

    /**
     * Description of route.
     * @return string
     */
    public function getRouteDescription()
    {
        return $this->routeDescription;
    }

    /**
     * Important stops on journey.
     * @return string
     */
    public function getVias()
    {
        return $this->vias;
    }

    /**
     * Reference to start point.
     * @return string
     */
    public function getOriginStopPointRef()
    {
        return $this->originStopPointRef;
    }

    /**
     * Name of start point.
     * @return \Chill\Util\Text
     */
    public function getOriginText()
    {
        return $this->originText;
    }

    /**
     * Reference to end point.
     * @return string
     */
    public function getDestinationStopPointRef()
    {
        return $this->destStopPointRef;
    }

    /**
     * Name of end point.
     * @return \Chill\Util\Text
     */
    public function getDestinationText()
    {
        return $this->destText;
    }

    /**
     * Concatenates name of mode and line name.
     *
     * Every line has public name and is realised by using a certain type of
     * vehicle. Together they give a good idea of what type of line this is.
     *
     * @return string Entire name of line.
     */
    public function getEntireLineName()
    {
        $name     = $this->getMode()->getName()->getText();
        $lineName = $this->getPublishedLineName()->getText();
        $glue     = '';

        if (empty($name) === false && empty($lineName) === false) {
            $glue = " ";
        }

        return $name.$glue.$lineName;
    }
}
