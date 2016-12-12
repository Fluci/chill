<?php
/**
 * Models: https://opentransportdata.swiss/de/cookbook/service-vdv-431/
 *
 * @category Travel
 * @package  Chill
 * @author   Felice Serena <felice@serena-mueller.ch>
 * @license  MIT License
 */
namespace Chill\Travel;

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
        if(!is_string($journeyRef)) {
            trigger_error("journeyRef must be string", E_ERROR);
        }

        if(!is_string($lineRef)) {
            trigger_error("lineRef must be string", E_ERROR);
        }

        if(!is_string($directionRef)) {
            trigger_error("directionRef must be string", E_ERROR);
        }

        if(!is_string($operatorRef)) {
            trigger_error("operatorRef must be string", E_ERROR);
        }

        $this->operatingDayRef = $operatingDayRef;
        $this->vehicleRef = $vehicleRef;
        $this->journeyRef = $journeyRef;
        $this->lineRef = $lineRef;
        $this->directionRef = $directionRef;
        $this->mode = $mode;
        $this->publishedLineName = $publishedLineName;
        $this->operatorRef = $operatorRef;
        $this->routeDescription = $routeDescription;
        $this->vias = $vias;
        $this->originStopPointRef = $originStopPointRef;
        $this->originText = $originText;
        $this->destStopPointRef = $destStopPointRef;
        $this->destText = $destText;
    }

    public function getOperatingDayRef() {
        return $this->operatingDayRef;
    }

    public function getVehicleRef() {
        return $this->vehicleRef;
    }

    public function getJourneyRef() {
        return $this->journeyRef;
    }

    public function getLineRef() {
        return $this->lineRef;
    }

    public function getDirectionRef() {
        return $this->directionRef;
    }

    public function getMode() {
        return $this->mode;
    }

    public function getPublishedLineName() {
        return $this->publishedLineName;
    }

    public function getOperatorRef() {
        return $this->operatorRef;
    }

    public function getRouteDescription() {
        return $this->routeDescription;
    }

    public function getVias() {
        return $this->vias;
    }

    public function getOriginStopPointRef() {
        return $this->originStopPointRef;
    }

    public function getOriginText() {
        return $this->originText;
    }

    public function getDestinationStopPointRef() {
        return $this->destStopPointRef;
    }

    public function getDestinationText() {
        return $this->destText;
    }

    public function getEntireLineName() {
        $name = $this->getMode()->getName()->getText();
        $lineName = $this->getPublishedLineName()->getText();
        $glue = "";
        if(!empty($name) && !empty($lineName)) {
            $glue = " ";
        }
        return $name.$glue.$lineName;
    }
}
