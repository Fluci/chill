<?php
/**
 * PHP version 5
 *
 * @category Test
 * @package  Chill
 * @author   Felice Serena <felice@serena-mueller.ch>
 * @license  MIT License
 */

namespace Chill\Travel;

class TravelFactorySimpleXmlTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateJourney()
    {
        $src = "<StopEventResult>
<StopEvent>

<PreviousCall>
    <CallAtStop>
        <StopPointRef>8500028</StopPointRef>
        <StopPointName>
            <Text>Tecknau</Text>
            <Language>DE</Language>
        </StopPointName>
        <ServiceArrival>
            <TimetabledTime>2016-08-18T02:54:00Z</TimetabledTime>
        </ServiceArrival>
        <ServiceDeparture>
            <TimetabledTime>2016-08-18T02:54:00Z</TimetabledTime>
        </ServiceDeparture>
        <StopSeqNumber>1</StopSeqNumber>
    </CallAtStop>
</PreviousCall>

<ThisCall>
    <CallAtStop>
        <StopPointRef>8502113</StopPointRef>
        <StopPointName>
            <Text>Aarau</Text>
            <Language>DE</Language>
        </StopPointName>
        <ServiceDeparture>
            <TimetabledTime>2016-11-10T09:24:00Z</TimetabledTime>
            <EstimatedTime>2016-11-10T09:24:00Z</EstimatedTime>
        </ServiceDeparture>
        <StopSeqNumber>2</StopSeqNumber>
    </CallAtStop>
</ThisCall>

<OnwardCall>
    <CallAtStop>
        <StopPointRef>8502192</StopPointRef>
        <StopPointName>
            <Text>Distelberg</Text>
            <Language>DE</Language>
        </StopPointName>
        <ServiceArrival>
            <TimetabledTime>2016-11-10T09:27:00Z</TimetabledTime>
        </ServiceArrival>
        <ServiceDeparture>
            <TimetabledTime>2016-11-10T09:27:00Z</TimetabledTime>
        </ServiceDeparture>
        <StopSeqNumber>4</StopSeqNumber>
    </CallAtStop>
</OnwardCall>
<Service>
    <OperatingDayRef>2016-12-09</OperatingDayRef>
    <JourneyRef>odp:01014::H:j16:250</JourneyRef>
    <LineRef>odp:01014::H</LineRef>
    <DirectionRef>outward</DirectionRef>
    <Mode>
        <PtMode>urbanRail</PtMode>
        <RailSubmode>suburbanRailway</RailSubmode>
        <Name>
            <Text>S-Bahn</Text>
            <Language>DE</Language>
        </Name>
    </Mode>
    <PublishedLineName>
        <Text>14</Text>
        <Language>DE</Language>
    </PublishedLineName>
    <OperatorRef>odp:96</OperatorRef>
    <OriginStopPointRef>
    </OriginStopPointRef>
    <OriginText>
        <Text>Menziken</Text>
        <Language>DE</Language>
    </OriginText>
    <DestinationStopPointRef>
    </DestinationStopPointRef>
    <DestinationText>
        <Text>Aarau</Text>
        <Language>DE</Language>
    </DestinationText>
</Service>
                    </StopEvent></StopEventResult>";
        $stopEvent = simplexml_load_string($src);

        $factory = new TravelFactorySimpleXml(new \DateTimeZone("Europe/Zurich"));
        $journey = $factory->createJourney($stopEvent);

        $this->assertEquals(1, count($journey->getPreviousCalls()));
        $this->assertEquals(1, count($journey->getThisCall()));
        $this->assertEquals(1, count($journey->getOnwardCall()));

        // check previous call
        $call = $journey->getPreviousCalls()[0];
        $this->assertEquals('8500028', $call->getStopPointRef());
        $this->assertEquals('Tecknau', $call->getStopPointName()->getText());
        $this->assertEquals('DE', $call->getStopPointName()->getLanguage());

        $this->assertNotEquals(null, $call->getServiceArrival());
        $this->assertNotEquals(null, $call->getServiceArrival()->getTimetabledTime());
        // should be same as timetabled
        $this->assertNotEquals(null, $call->getServiceArrival()->getEstimatedTime());

        $this->assertNotEquals(null, $call->getServiceDeparture());
        $this->assertNotEquals(null, $call->getServiceDeparture()->getTimetabledTime());
        $this->assertNotEquals(null, $call->getServiceDeparture()->getEstimatedTime());

        $this->assertEquals('1', $call->getStopSeqNumber());

        // check thisCall
        $call = $journey->getThisCall();
        $this->assertEquals('8502113', $call->getStopPointRef());
        $this->assertEquals('Aarau', $call->getStopPointName()->getText());
        $this->assertEquals('DE', $call->getStopPointName()->getLanguage());

        $this->assertEquals(null, $call->getServiceArrival());

        $this->assertNotEquals(null, $call->getServiceDeparture());
        $this->assertNotEquals(null, $call->getServiceDeparture()->getTimetabledTime());
        $this->assertNotEquals(null, $call->getServiceDeparture()->getEstimatedTime());

        $this->assertEquals('2', $call->getStopSeqNumber());


        // check onward call
        $call = $journey->getOnwardCall()[0];
        $this->assertEquals('8502192', $call->getStopPointRef());
        $this->assertEquals('Distelberg', $call->getStopPointName()->getText());
        $this->assertEquals('DE', $call->getStopPointName()->getLanguage());

        $this->assertNotEquals(null, $call->getServiceArrival()->getTimetabledTime());
        $this->assertNotEquals(null, $call->getServiceArrival()->getEstimatedTime());

        $this->assertNotEquals(null, $call->getServiceDeparture()->getTimetabledTime());
        $this->assertNotEquals(null, $call->getServiceDeparture()->getEstimatedTime());

        $this->assertEquals('4', $call->getStopSeqNumber());

        // check service
        $service = $journey->getService();
        $this->assertNotEquals(null, $service);
        $this->assertEquals('2016-12-09', $service->getOperatingDayRef()->format('Y-m-d'));
        $this->assertEquals('odp:01014::H:j16:250', $service->getJourneyRef());
        $this->assertEquals('odp:01014::H', $service->getLineRef());
        $this->assertEquals('outward', $service->getDirectionRef());

        $this->assertEquals('urbanRail', $service->getMode()->getPtMode());
        $this->assertEquals('suburbanRailway', $service->getMode()->getRailSubmode());
        $this->assertEquals('S-Bahn', $service->getMode()->getName()->getText());
        $this->assertEquals('DE', $service->getMode()->getName()->getLanguage());

        $this->assertEquals('14', $service->getPublishedLineName()->getText());
        $this->assertEquals('DE', $service->getPublishedLineName()->getLanguage());

        $this->assertEquals('odp:96', $service->getOperatorRef());

        $this->assertEquals(null, $service->getOriginStopPointRef());

        $this->assertEquals('Menziken', $service->getOriginText()->getText());
        $this->assertEquals('DE', $service->getOriginText()->getLanguage());

        $this->assertEquals(null, $service->getDestinationStopPointRef());

        $this->assertEquals('Aarau', $service->getDestinationText()->getText());
        $this->assertEquals('DE', $service->getDestinationText()->getLanguage());
    }

    public function testCreateDateTime(){
        $src = "<ServiceDeparture>
            <TimetabledTime>2016-11-10T09:24:00Z</TimetabledTime>
            <EstimatedTime>2016-11-10T09:24:30Z</EstimatedTime>
        </ServiceDeparture>";
        $serviceDeparture = simplexml_load_string($src);
        $factory = new TravelFactorySimpleXml(new \DateTimeZone("Europe/Zurich"));
        $time = $factory->createDateTime($serviceDeparture);

        $this->assertEquals('2016-11-10T09:24:00Z', gmdate('Y-m-d\TH:i:s\Z', $time->getTimetabledTime()->getTimestamp()));
        $this->assertEquals('2016-11-10T09:24:30Z', gmdate('Y-m-d\TH:i:s\Z', $time->getEstimatedTime()->getTimestamp()));
    }

    public function testCreateDateTimeEstimatedMissing(){
        $src = "<ServiceDeparture>
            <TimetabledTime>2016-11-10T09:24:00Z</TimetabledTime>
        </ServiceDeparture>";
        $serviceDeparture = simplexml_load_string($src);
        $factory = new TravelFactorySimpleXml(new \DateTimeZone("Europe/Zurich"));
        $time = $factory->createDateTime($serviceDeparture);

        $this->assertEquals('2016-11-10T09:24:00Z', gmdate('Y-m-d\TH:i:s\Z', $time->getTimetabledTime()->getTimestamp()));
        $this->assertEquals('2016-11-10T09:24:00Z', gmdate('Y-m-d\TH:i:s\Z', $time->getEstimatedTime()->getTimestamp()));
    }

}

?>
