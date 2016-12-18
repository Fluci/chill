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
 * Models a type of transportation vehicle:
 * https://opentransportdata.swiss/de/cookbook/ptmode/
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
    const PTMODE_UNKNOWN        = 'unknown';
    const PTMODE_AIR            = 'air';
    const PTMODE_BUS            = 'bus';
    const PTMODE_TROLLEYBUS     = 'trolleybus';
    const PTMODE_TRAM           = 'tram';
    const PTMODE_COACH          = 'coach';
    const PTMODE_RAIL           = 'rail';
    const PTMODE_INTERCITY_RAIL = 'intercityRail';
    const PTMODE_URBAN_RAIL     = 'urbanRail';
    const PTMODE_METRO          = 'metro';
    const PTMODE_WATER          = 'water';
    const PTMODE_CABLEWAY       = 'cableway';
    const PTMODE_FUNICULAR      = 'funicular';
    const PTMODE_TAXI           = 'taxi';

    private $ptMode = self::PTMODE_UNKNOWN;
    private $railSubmode;
    private $name;


    /**
     * Creates a Mode object. It takes a mode (see PTMODE constants) describing
     * the rought type of the vehicle and a railSubmode which tries to refine the
     * description.
     * @param string          $ptMode      Rough vehicle type, See `PTMODE_` consts.
     * @param string          $railSubmode Additional description refining the type.
     * @param Chill\Util\Text $name        Name of the vehicle.
     */
    public function __construct($ptMode, $railSubmode, \Chill\Util\Text $name)
    {
        $this->ptMode      = $ptMode;
        $this->railSubmode = $railSubmode;
        $this->name        = $name;
    }

    /**
     * Returns rough type of vehicle. See `PTMODE_` constants.
     * @return string
     */
    public function getPtMode()
    {
        return $this->ptMode;
    }

    /**
     * Description of subtype of vehicle.
     * @return string
     */
    public function getRailSubmode()
    {
        return $this->railSubmode;
    }

    /**
     * Name of vehicle.
     * @return \Chill\Util\Text
     */
    public function getName()
    {
        return $this->name;
    }

}
