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

class Journey
{
	private $previousCalls = null;
	private $thisCall = null;
	private $onwardCalls = null;

	public function __construct($previousCalls, $thisCall, $onwardCalls) {
		$this->previousCalls = $previousCalls;
		$this->thisCall = $thisCall;
		$this->onwardCalls = $onwardCalls;
	}

	public function getPreviousCalls() {
		return $this->previousCalls;
	}
	public function getPreviousCall() {
		return $this->getPreviousCalls();
	}
	public function getThisCall() {
		return $this->thisCall;
	}
	public function getOnwardCalls() {
		return $this->onwardCalls;
	}
	public function getOnwardCall() {
		return $this->getOnwardCalls();
	}
}
