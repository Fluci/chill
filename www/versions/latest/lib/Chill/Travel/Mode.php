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
 * Models transportation vehicle:
 * https://opentransportdata.swiss/de/cookbook/service-vdv-431/
 *
 * <Mode>
 *       <PtMode>urbanRail</PtMode>
 *       <RailSubmode>suburbanRailway</RailSubmode>
 *       <Name>
 *           <Text>S-Bahn</Text>
 *           <Language>DE</Language>
 *       </Name>
 *   </Mode>
 *
 * @category Travel
 * @package  Chill
 */
class Mode
{
	const PTMODE_UNKNOWN = 'unknown';
	const PTMODE_AIR = 'air';
	const PTMODE_BUS = 'bus';
	const PTMODE_TROLLEYBUS = 'trolleybus';
	const PTMODE_TRAM = 'tram';
	const PTMODE_COACH = 'coach';
	const PTMODE_RAIL = 'rail';
	const PTMODE_INTERCITY_RAIL = 'intercityRail';
	const PTMODE_URBAN_RAIL = 'urbanRail';
	const PTMODE_METRO = 'metro';
	const PTMODE_WATER = 'water';
	const PTMODE_CABLEWAY = 'cableway';
	const PTMODE_FUNICULAR = 'funicular';
	const PTMODE_TAXI = 'taxi';

	private $ptMode = self::PTMODE_UNKNOWN;
	private $railSubmode;
	private $name;

	public function __construct($ptMode, $railSubmode, \Chill\Util\Text $name) {
		$this->ptMode = $ptMode;
		$this->railSubmode = $railSubmode;
		$this->name = $name;
	}

	public function getPtMode() {
		return $this->ptMode;
	}

	public function getRailSubmode() {
		return $this->railSubmode;
	}

	public function getName() {
		return $this->name;
	}

}
