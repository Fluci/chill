<?php
/**
 * Models a timetable entry, it has a planned=timetabled entry (when the train should arrive)
 * and an estimated time (when it probably will arrive)
 *
 * @category Travel
 * @package  Chill
 * @author   Felice Serena <felice@serena-mueller.ch>
 * @license  MIT License
 */
namespace Chill\Travel;

class DateTime
{
	private $timetabled = null;
	private $estimated = null;

	public function __construct(\DateTime $timetabled, \DateTime $estimated = null) {
		$this->timetabled = $timetabled;
		if($estimated == null) {
			$estimated = $timetabled;
		}
		$this->estimated = $estimated;
	}

	public function getTimetabledTime() {
		return $this->timetabled;
	}
	public function getEstimatedTime() {
		return $this->estimated;
	}

	public function getOverdue() {
		return $this->estimated->getTimestamp() - $this->timetabled->getTimestamp();
	}
}
