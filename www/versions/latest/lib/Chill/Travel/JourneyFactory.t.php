<?php
/**
 * 
 *
 * @category Test
 * @package  Chill
 * @author   Felice Serena <felice@serena-mueller.ch>
 * @license  MIT License
 */

namespace Chill\Travel;

class JourneyFactoryTest extends \PHPUnit_Framework_TestCase
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
                    </StopEvent></StopEventResult>";
    	$stopEvent = simplexml_load_string($src);
	
		$factory = new JourneyFactory(new \DateTimeZone(\DateTimeZone::EUROPE));
		$journey = $factory->createJourneyFromResponseTree($stopEvent);

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
		$this->assertEquals(null, $call->getServiceArrival()->getEstimatedTime());

		$this->assertNotEquals(null, $call->getServiceDeparture());
		$this->assertNotEquals(null, $call->getServiceDeparture()->getTimetabledTime());
		$this->assertEquals(null, $call->getServiceDeparture()->getEstimatedTime());

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
		$this->assertEquals(null, $call->getServiceArrival()->getEstimatedTime());

		$this->assertNotEquals(null, $call->getServiceDeparture()->getTimetabledTime());
		$this->assertEquals(null, $call->getServiceDeparture()->getEstimatedTime());

		$this->assertEquals('4', $call->getStopSeqNumber());
    }
    
    public function testCreateDateTime(){
    	$src = "<ServiceDeparture>
            <TimetabledTime>2016-11-10T09:24:00Z</TimetabledTime>
            <EstimatedTime>2016-11-10T09:24:30Z</EstimatedTime>
        </ServiceDeparture>";
    	$serviceDeparture = simplexml_load_string($src);
    	$factory = new JourneyFactory(new \DateTimeZone(\DateTimeZone::EUROPE));
    	$time = $factory->createDateTimeFromResponseTree($serviceDeparture);
    
    	$this->assertEquals('2016-11-10T09:24:00Z', gmdate('Y-m-d\TH:i:s\Z', $time->getTimetabledTime()->getTimestamp()));
    	$this->assertEquals('2016-11-10T09:24:30Z', gmdate('Y-m-d\TH:i:s\Z', $time->getEstimatedTime()->getTimestamp()));
    }
    
    public function testCreateDateTimeEstimatedMissing(){
    	$src = "<ServiceDeparture>
            <TimetabledTime>2016-11-10T09:24:00Z</TimetabledTime>
        </ServiceDeparture>";
    	$serviceDeparture = simplexml_load_string($src);
    	$factory = new JourneyFactory(new \DateTimeZone(\DateTimeZone::EUROPE));
    	$time = $factory->createDateTimeFromResponseTree($serviceDeparture);

    	$this->assertEquals('2016-11-10T09:24:00Z', gmdate('Y-m-d\TH:i:s\Z', $time->getTimetabledTime()->getTimestamp()));
    	$this->assertEquals(null, $time->getEstimatedTime());
    }

}

?>
