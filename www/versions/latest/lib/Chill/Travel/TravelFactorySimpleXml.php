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
 * Converts a tree of SimpleXMLElements to internal objects.
 *
 * @category Travel
 * @package  Chill
 */
class TravelFactorySimpleXml
{
    private $timezone;


    /**
     * Creates new TravelFactory for simple xml requests (\SimpleXMLElement).
     * Converts \SimpleXMLElements to the corresponding internal objects.
     * @param  int $timezone Reference timezone to use.
     * @return void                 nothing
     */
    public function __construct(\DateTimeZone $timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * Root should be a StopEventResult
     * @param  SimpleXMLElement $jRoot Element received from xml request.
     * @return \Chill\Travel\Journey   Internal representation for a journey.
     */
    public function createJourney(\SimpleXMLElement $jRoot)
    {
        $previousCalls = array();
        foreach ($jRoot->StopEvent->PreviousCall as $stopPoint) {
            $previousCalls[] = $this->createStopPoint($stopPoint->CallAtStop);
        }

        $thisCallStop = $jRoot->StopEvent->ThisCall->CallAtStop;
        $thisCall     = $this->createStopPoint($thisCallStop);
        $onwardCalls  = array();
        foreach ($jRoot->StopEvent->OnwardCall as $stopPoint) {
            $onwardCalls[] = $this->createStopPoint($stopPoint->CallAtStop);
        }

        $service = $this->createService($jRoot->StopEvent->Service);
        return new Journey($previousCalls, $thisCall, $onwardCalls, $service);
    }

    /**
     * Creates an internal stopPoint object from a given simple xml element.
     * @param  SimpleXMLElement $spRoot XML-tree representation of stopPoint.
     * @return \Chill\Travel\StopPoint  Internal representation of a stopPoint.
     */
    public function createStopPoint(\SimpleXMLElement $spRoot)
    {
        $ref  = $this->val($spRoot->StopPointRef);
        $name = $this->createText($spRoot->StopPointName);

        $arrival = null;
        if ($spRoot->ServiceArrival->count() > 0) {
            $arrival = $this->createDateTime($spRoot->ServiceArrival);
        }

        $departure = null;
        if ($spRoot->ServiceDeparture->count() > 0) {
            $departure = $this->createDateTime($spRoot->ServiceDeparture);
        }

        $seqNumber = $this->val($spRoot->StopSeqNumber);

        return new StopPoint($ref, $name, $seqNumber, $arrival, $departure);
    }

    /**
     * Converts a simple xml element to an internal representation.
     * @param  SimpleXMLElement $tRoot Element from request.
     * @return \Chill\Util\Text        Internal representation of text object.
     */
    public function createText(\SimpleXMLElement $tRoot)
    {
        $text = $this->val($tRoot->Text);
        $lang = $this->val($tRoot->Language);

        return new \Chill\Util\Text($text, $lang);
    }

    /**
     * Creates a Travel DateTime from a simple xml element from a request.
     * @param  SimpleXMLElement $tRoot Root element representing an event time.
     * @return \Chill\Travel\DateTime  Internal representation for an event time.
     */
    public function createDateTime(\SimpleXMLElement $tRoot)
    {

        // Mandatory field.
        $rawTimetabled = $this->val($tRoot->TimetabledTime);

        $timetabled = new \DateTime($rawTimetabled, $this->timezone);

        $estimated = $this->val($tRoot->EstimatedTime);

        if ($estimated !== null) {
            $estimated = new \DateTime($estimated, $this->timezone);
        }

        return new \Chill\Travel\DateTime($timetabled, $estimated);
    }

    /**
     * Converts a simpleXMLElement to a mode object.
     * @param  SimpleXMLElement $ser Mode as simpleXMLElement from a request.
     * @return \Chill\Travel\Mode    Internal object for Mode representation.
     */
    public function createMode(\SimpleXMLElement $ser)
    {
        $ptMode = $this->val($ser->PtMode);

        $railSubmode = $this->val($ser->RailSubmode);

        $name = $this->createText($ser->Name);

        return new \Chill\Travel\Mode($ptMode, $railSubmode, $name);
    }

    /**
     * Converts a given service tree to the internal representation.
     * @param  SimpleXMLElement $ser From <service> in a request.
     * @return \Chill\Travel\Service Internal object for Services.
     */
    public function createService(\SimpleXMLElement $ser)
    {
        $rawOperDayRef   = $this->val($ser->OperatingDayRef);
        $operatingDayRef = new \DateTime($rawOperDayRef, $this->timezone);
        $vehicleRef      = $this->val($ser->VehicleRef);
        $journeyRef      = $this->val($ser->JourneyRef);
        $lineRef         = $this->val($ser->LineRef);
        $directionRef    = $this->val($ser->DirectionRef);

        $mode = $this->createMode($ser->Mode);

        $publishedLineName = $this->createText($ser->PublishedLineName);
        $operatorRef       = $this->val($ser->OperatorRef);
        $routeDescription  = $this->val($ser->RouteDescription);

        $vias = $ser->RouteDescription;

        $originStopPointRef = $this->val($ser->OriginStopPointRef);
        $originText         = $this->createText($ser->OriginText);

        $dstStopPointRef = $this->val($ser->DestinationStopPointRef);
        $dstText         = $this->createText($ser->DestinationText);

        return new \Chill\Travel\Service(
            $operatingDayRef,
            $vehicleRef,
            $journeyRef,
            $lineRef,
            $directionRef,
            $mode,
            $publishedLineName,
            $operatorRef,
            $routeDescription,
            $vias,
            $originStopPointRef,
            $originText,
            $dstStopPointRef,
            $dstText
        );
    }

    /**
     * Extract value from SimpleXMLElement. Wraps basic checking
     * which is necessary since a child node is null when not available.
     * Default is taken if the text representation is '' or the object doesn't
     * exists (is null).
     * @param  \SimpleXMLElement $tRoot   Element to convert to text representation.
     * @param  string            $default Default value to choose
     *                                    if it can't be required from $tRoot.
     * @return string                     Found text representation.
     */
    private function val(\SimpleXMLElement $tRoot = null, $default = null)
    {
        $ret = '';
        if ($tRoot !== null) {
            $ret = trim($tRoot->__toString());
        }

        if ($ret === '') {
            return $default;
        }

        return $ret;
    }
}
