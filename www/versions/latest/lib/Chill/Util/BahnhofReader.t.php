<?php
/**
 *
 *
 * @category Test
 * @package  Chill
 * @author   Felice Serena <felice@serena-mueller.ch>
 * @license  MIT License
 */

namespace Chill\Util;

class BahnhofReaderTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyNameStr()
    {
        $reader = new BahnhofReader();

        $result = $reader->readNamesStr('');

        $expected = array();
        $this->assertEquals($expected, $result);
    }

    public function testNormal1()
    {
        $reader = new BahnhofReader();

        $result = $reader->readLine('0000011,Grenchen$<1>');

        $expected = array(
            array('stopPointRef' => '0000011', 'stopPointName' => 'Grenchen'),
        );
        $this->assertEquals($expected, $result);
    }

    public function testQuotes()
    {
        $reader = new BahnhofReader();

        $result = $reader->readLine('0000006,"Feldbrunnen, St. Katharinen$<1>"');

        $expected = array(array('stopPointRef' => '0000006', 'stopPointName' => 'Feldbrunnen, St. Katharinen'));
        $this->assertEquals($expected, $result);
    }

    public function testDuplicate()
    {
        $reader = new BahnhofReader();

        $result = $reader->readLine('0000132,Bahn-2000-Strecke$<1>$Bahn-2000-Strecke$<2>');

        $expected = array(
            array('stopPointRef' => '0000132', 'stopPointName' => 'Bahn-2000-Strecke')
        );
        $this->assertEquals($expected, $result);
    }

    public function testNormal3Ignore()
    {
        $reader = new BahnhofReader();

        $result = $reader->readLine('8002371,Lindau Hbf$<1>$LIND$<3>');

        $expected = array(
            array('stopPointRef' => '8002371', 'stopPointName' => 'Lindau Hbf')
        );
        $this->assertEquals($expected, $result);
    }

    public function testNormal4()
    {
        $reader = new BahnhofReader();

        $result = $reader->readLine('8014450,Zell (Wiesental)$<1>$Zell (W)$<4>$Zell im Wiesental$<4>$ZE$<3>');

        $expected = array(
            array('stopPointRef' => '8014450', 'stopPointName' => 'Zell (Wiesental)'),
            array('stopPointRef' => '8014450', 'stopPointName' => 'Zell (W)'),
            array('stopPointRef' => '8014450', 'stopPointName' => 'Zell im Wiesental')
        );
        $this->assertEquals($expected, $result);
    }
}
