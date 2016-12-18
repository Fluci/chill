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
 * Models a station halt (when, where, etc.)
 *
 * @category Travel
 * @package  Chill
 */
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
