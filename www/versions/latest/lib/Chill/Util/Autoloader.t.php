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

class AutoloaderTest extends \PHPUnit_Framework_TestCase
{
    public function testSetFlushNamespace()
    {
        $loader = new Autoloader();
        $this->assertEquals($loader, $loader->addNamespace("something", "dir"));
        $this->assertEquals($loader, $loader->flushNamespaces());

    }
    public function testLoadExistingClass()
    {
        $loader = new Autoloader();
        $this->assertEquals($loader, $loader->addNamespace('\\Chill\\Util\\', __DIR__));
    }
    public function testLoadNonExistent()
    {
        $loader = new Autoloader();
        $this->assertEquals($loader, $loader->addNamespace('\\Chill\\', '.'));
        $this->assertEquals(false, $loader->load("Chill\\DoesNotExist"));
    }
    public function testLoadNonExistentPrefix()
    {
        $loader = new Autoloader();
        $this->assertEquals($loader, $loader->addNamespace('\\Chill\\', '.'));
        $this->assertEquals(false, $loader->load("blub\\DoesNotExist"));
    }
    public function testRegister()
    {
        $loader = new Autoloader();
        $loader->register(); // nothing crashes
        $this->assertTrue(true); // strickt test
    }
}
