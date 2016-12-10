<?php
/**
 * Models a station halt (when, where, etc.)
 *
 * @category Travel
 * @package  Chill
 * @author   Felice Serena <felice@serena-mueller.ch>
 * @license  MIT License
 */
namespace Chill\Travel;

class JourneyFactory
{
	private $timezone;

	public function __constructor(\DateTimeZone  $timezone) {
		$this->timezone = new \DateTimeZone($timezone);
	}
	
	/**
	* Root should be a StopEventResult
	*
	**/
	public function createJourneyFromResponseTree(\SimpleXMLElement $j) {
		$previousCalls = array();
		foreach($j->StopEvent->PreviousCall as $stopPoint) {
			$previousCalls[] = $this->createStopPointFromResponseTree($stopPoint->CallAtStop);
		}
		$thisCall = $this->createStopPointFromResponseTree($j->StopEvent->ThisCall->CallAtStop);
		$onwardCalls = array();
		foreach($j->StopEvent->OnwardCall as $stopPoint) {
			$onwardCalls[] = $this->createStopPointFromResponseTree($stopPoint->CallAtStop);
		}
		return new Journey($previousCalls, $thisCall, $onwardCalls);
	}

	public function createStopPointFromResponseTree(\SimpleXMLElement $p) {
		$ref = $this->val($p->StopPointRef);
		$name = $this->createTextFromResponseTree($p->StopPointName);
		$arrival = null;
		if($p->ServiceArrival->count() > 0) {
			$arrival = $this->createDateTimeFromResponseTree($p->ServiceArrival);
		}
		$departure = null;
		if($p->ServiceDeparture->count() > 0) {
			$departure = $this->createDateTimeFromResponseTree($p->ServiceDeparture);
		}
		$seqNumber = $this->val($p->StopSeqNumber);
		
		return new StopPoint($ref, $name, $arrival, $departure, $seqNumber);
	}

	public function createTextFromResponseTree(\SimpleXMLElement $t) {
		$text = $this->val($t->Text);
		$lang = $this->val($t->Language);
		
		return new \Chill\Util\Text($text, $lang);
	}

	public function createDateTimeFromResponseTree(\SimpleXMLElement $t) {
		
		// mandatory field
		$timetabled = new \DateTime($this->val($t->TimetabledTime), $this->timezone);
		
		$estimated = $this->val($t->EstimatedTime);

		if($estimated === "") {
			$estimated = null;
		}

		if($estimated !== null) {
			$estimated = new \DateTime($estimated, $this->timezone);
		}
		
		return new \Chill\Travel\DateTime($timetabled, $estimated);
	}

	private function val(\SimpleXMLElement $t = null) {
		return ($t === null ? null : $t->__toString());
	}
}
