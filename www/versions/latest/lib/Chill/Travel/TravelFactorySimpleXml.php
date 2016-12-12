<?php
/**
 * Converts a tree of SimpleXMLElements to internal objects.
 *
 * @category Travel
 * @package  Chill
 * @author   Felice Serena <felice@serena-mueller.ch>
 * @license  MIT License
 */
namespace Chill\Travel;

class TravelFactorySimpleXml
{
	private $timezone;

	public function __constructor(\DateTimeZone  $timezone) {
		$this->timezone = new \DateTimeZone($timezone);
	}
	
	/**
	* Root should be a StopEventResult
	*
	**/
	public function createJourney(\SimpleXMLElement $j) {
		$previousCalls = array();
		foreach($j->StopEvent->PreviousCall as $stopPoint) {
			$previousCalls[] = $this->createStopPoint($stopPoint->CallAtStop);
		}
		$thisCall = $this->createStopPoint($j->StopEvent->ThisCall->CallAtStop);
		$onwardCalls = array();
		foreach($j->StopEvent->OnwardCall as $stopPoint) {
			$onwardCalls[] = $this->createStopPoint($stopPoint->CallAtStop);
		}

		$service = $this->createService($j->StopEvent->Service);
		return new Journey($previousCalls, $thisCall, $onwardCalls, $service);
	}

	public function createStopPoint(\SimpleXMLElement $p) {
		$ref = $this->val($p->StopPointRef);
		$name = $this->createText($p->StopPointName);
		$arrival = null;
		if($p->ServiceArrival->count() > 0) {
			$arrival = $this->createDateTime($p->ServiceArrival);
		}
		$departure = null;
		if($p->ServiceDeparture->count() > 0) {
			$departure = $this->createDateTime($p->ServiceDeparture);
		}
		$seqNumber = $this->val($p->StopSeqNumber);
		
		return new StopPoint($ref, $name, $arrival, $departure, $seqNumber);
	}

	public function createText(\SimpleXMLElement $t) {
		$text = $this->val($t->Text);
		$lang = $this->val($t->Language);
		
		return new \Chill\Util\Text($text, $lang);
	}

	public function createDateTime(\SimpleXMLElement $t) {
		
		// mandatory field
		$timetabled = new \DateTime($this->val($t->TimetabledTime), $this->timezone);
		
		$estimated = $this->val($t->EstimatedTime);

		if($estimated !== null) {
			$estimated = new \DateTime($estimated, $this->timezone);
		}
		
		return new \Chill\Travel\DateTime($timetabled, $estimated);
	}

	public function createMode(\SimpleXMLElement $ser) {
		$ptMode = $this->val($ser->PtMode);
		$railSubmode = $this->val($ser->RailSubmode);
		$name = $this->createText($ser->Name);
		
		return new \Chill\Travel\Mode($ptMode, $railSubmode, $name);
	}

	public function createService(\SimpleXMLElement $ser) {
		$operatingDayRef = new \DateTime($this->val($ser->OperatingDayRef), $this->timezone);
		$vehicleRef = $this->val($ser->VehicleRef);
		$journeyRef = $this->val($ser->JourneyRef);
		$lineRef = $this->val($ser->LineRef);
		$directionRef = $this->val($ser->DirectionRef);
		
		$mode = $this->createMode($ser->Mode);
		
		$publishedLineName = $this->createText($ser->PublishedLineName);
		$operatorRef = $this->val($ser->OperatorRef);
		$routeDescription = $this->val($ser->RouteDescription);
		$vias = $ser->RouteDescription;

		$originStopPointRef = $this->val($ser->OriginStopPointRef);
		$originText = $this->createText($ser->OriginText);

		$destinationStopPointRef = $this->val($ser->DestinationStopPointRef);
		$destinationText = $this->createText($ser->DestinationText);

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
			$destinationStopPointRef, 
			$destinationText
		);
	}

	private function val(\SimpleXMLElement $t = null, $default = null) {
		$ret = ($t === null ? null : trim($t->__toString()));
		return $ret === "" ? $default : $ret;
	}
}
