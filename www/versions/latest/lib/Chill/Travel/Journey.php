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
class Journey
{
	private $previousCalls = null;
	private $thisCall = null;
	private $onwardCalls = null;
	private $service = null;

	public function __construct($previousCalls, $thisCall, $onwardCalls, $service) {
		$this->previousCalls = $previousCalls;
		$this->thisCall = $thisCall;
		$this->onwardCalls = $onwardCalls;
		$this->service = $service;
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

	public function getService() {
		return $this->service;
	}
}
