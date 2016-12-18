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
 * Models a timetable entry.
 *
 * It has a planned=timetabled entry (when the train should arrive)
 * and an estimated time (when it probably will arrive).
 *
 * @category Travel
 * @package  Chill
 */
class DateTime
{
    /**
     * Point in time when an event is scheduled to happen.
     * @var \DateTime
     */
    private $timetabled = null;

    /**
     * Point in time when an event is expected to actually happen.
     * @var \DateTime
     */
    private $estimated = null;


    /**
     * Creates \Chill\Travel\DateTime object with a timetabled value of
     * type \DateTime. If no $estimated is given, $timetabled is used.
     * @param \DateTime $timetabled Time, vehicle is expected.
     * @param \DateTime $estimated  If null is passed, $timetabled is taken instead.
     */
    public function __construct(\DateTime $timetabled, \DateTime $estimated = null)
    {
        $this->timetabled = $timetabled;
        if ($estimated === null) {
            $estimated = $timetabled;
        }

        $this->estimated = $estimated;
    }

    /**
     * Timetabled time: Time that is written in the schedule.
     * It was planned that the event should happen at this time.
     *
     * @return \DateTime Time when event should have happened.
     */
    public function getTimetabledTime()
    {
        return $this->timetabled;
    }

    /**
     * Estimated time: Time at which the event is expected to happen
     * or did happen (if already in past).
     *
     * @return \DateTime Time when event is expected to happen/did happen.
     */
    public function getEstimatedTime()
    {
        return $this->estimated;
    }

    /**
     * Formats a given time in seconds to be displayed in `H:i` format where
     * `H` is only shown when unequal zero. If a negative amount of seconds is
     * given, a minus is prepended.
     * @param  int $secs Time given in seconds.
     * @return string       Time formated in `-H:i`.
     */
    private function formatTime($secs)
    {
        $sign = '';
        if ($secs < 0) {
            $sign = '-';
        }

        $secs = abs($secs);
        $mins = gmdate("i", $secs);
        $hrs  = gmdate("H", $secs);
        $out  = "";

        if ($hrs !== '00') {
            $out .= $hrs.":";
        }

        $out .= $mins;
        return $sign.$out;
    }

    /**
     * Gives time difference in seconds between timetabled and estimated:
     * `estimated - timetabled`
     *
     * @return int Seconds between timetabled and estimated.
     */
    public function getOverdue()
    {
        return $this->estimated->getTimestamp() - $this->timetabled->getTimestamp();
    }

    /**
     * Formats overdue duration as `H:i` where the hour is optional.
     * @return string Formatted string.
     */
    public function getOverdueFormat()
    {
        return $this->formatTime($this->getOverdue());
    }

    /**
     * Gives time difference in seconds between now and timetabled:
     * `timetabled - now`
     *
     * @return int Seconds until timetabled event.
     */
    public function getDurationUntilTimetabled()
    {
        return $this->timetabled->getTimestamp() - time();
    }

    /**
     * Formats durationUntilTimetabled as seconds with optional leading hour.
     * When timetabled is in the future, a minus is prepended.
     * @return string `H:i` formatted string.
     */
    public function getDurationUntilTimetabledFormat()
    {
        return $this->formatTime(-$this->getDurationUntilTimetabled());
    }

    /**
     * Gives time difference in seconds between now and estimated:
     * `estimated - now`
     *
     * @return int Seconds until estimated event.
     */
    public function getDurationUntilEstimated()
    {
        return $this->estimated->getTimestamp() - time();
    }

    /**
     * Formats durationUntilEstimated as seconds with optional leading hour.
     * When estimated is in the future, a minus is prepended.
     * @return string `H:i` formatted string.
     */
    public function getDurationUntilEstimatedFormat()
    {
        return $this->formatTime(-$this->getDurationUntilEstimated());
    }
}
