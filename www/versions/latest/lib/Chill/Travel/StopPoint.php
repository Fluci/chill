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

class StopPoint
{
	private $ref = -1;
	private $name = null;
	private $arrival = null;
	private $departure = null;
	private $seqNumber = -1;

	public function __construct($ref, $name, $arrival = null, $departure = null, $seqNumber) {
		$this->ref = $ref;
		$this->name = $name;
		$this->arrival = $arrival;
		$this->departure = $departure;
		$this->seqNumber = $seqNumber;
	}

	public function getStopPointRef() {
		return $this->ref;
	}
	public function getStopPointName() {
		return $this->name;
	}
	public function getServiceArrival() {
		return $this->arrival;
	}
	public function getServiceDeparture() {
		return $this->departure;
	}
	public function getStopSeqNumber() {
		return $this->seqNumber;
	}
}
