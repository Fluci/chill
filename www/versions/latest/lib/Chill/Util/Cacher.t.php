<?php
/**
 * PHP version 5
 *
 * @category Test
 * @package  Chill
 * @author   Felice Serena <felice@serena-mueller.ch>
 * @license  http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Chill\Util;

class TestCacher extends Cacher {
    private $cacheAtomCallback = null;

    public function setCallback($callback)
    {
        $this->cacheAtomCallback = $callback;
    }

    protected function cacheAtomCompare(
        \Chill\Util\CacheAtom $new,
        \Chill\Util\CacheAtom $old = null
    ) {
        $func = $this->cacheAtomCallback;
        return $func($new, $old);
    }
}

class CacherTest extends \PHPUnit_Framework_TestCase
{

    private $callbackVersionEq;

    public function setUp()
    {
        $that = $this;
        $this->callbackVersionEq = function($new, $old) use ($that) {
            if ($old === null) {
                return false;
            }

            return $new->getVersion() === $old->getVersion();
        };
    }

    public function testStoreLoadSuccess()
    {
        // Set up
        $fileSys = new \VirtualFileSystem\FileSystem();
        $root = $fileSys->path('/root');
        $cacher = new TestCacher($root, 10);

        $cacher->setCallback($this->callbackVersionEq);

        // Test
        $cacher->store('aKey', 'aVersion', 'somePayload');

        // Make sure we get the same thing back.
        $payload = $cacher->loadCached('aKey', 'aVersion');

        $this->assertEquals('somePayload', $payload);
    }

    public function testFetchCached()
    {
        // Set up
        $fileSys = new \VirtualFileSystem\FileSystem();
        $root = $fileSys->path('/root');
        $cacher = new TestCacher($root, 10);

        $cacher->setCallback($this->callbackVersionEq);

        // Test
        $cacher->store('aKey', 'aVersion', 'somePayload');

        // Make sure we get the same thing back.
        $payload = $cacher->loadCached('aKey', 'aVersion');

        $this->assertEquals('somePayload', $payload);
    }

    public function testLoadFailNoItem()
    {
        // Set up
        $fileSys = new \VirtualFileSystem\FileSystem();
        $root = $fileSys->path('/root');
        $cacher = new TestCacher($root, 10);

        $cacher->setCallback($this->callbackVersionEq);

        // Test
        // No item directory there.
        $found = $cacher->loadCached('aKey', 'aVersion');

        $this->assertFalse($found);
    }

    public function testLoadFailNoAtom()
    {
        // Set up
        $fileSys = new \VirtualFileSystem\FileSystem();
        $root = $fileSys->path('/root');
        $cacher = new TestCacher($root, 10);

        // Create key directory.
        mkdir($fileSys->path('/root/aKey'), 0777, true);

        $cacher->setCallback($this->callbackVersionEq);

        // Test
        // No cache atom there.
        $found = $cacher->loadCached('aKey', 'aVersion');

        $this->assertFalse($found);
    }

    public function testLoadFailOldAtom()
    {
        // Set up
        $fileSys = new \VirtualFileSystem\FileSystem();
        $root = $fileSys->path('/root');
        $cacher = new TestCacher($root, -1);

        $cacher->setCallback($this->callbackVersionEq);

        // Store an atom.
        $cacher->store('aKey', 'aVersion', 'oldContent: don\'t fetch');

        // Test
        // No cache atom there.
        $found = $cacher->loadCached('aKey', 'aVersion');

        $this->assertFalse($found);
    }

    public function testLoadFailWrongVersion()
    {
        // Set up
        $fileSys = new \VirtualFileSystem\FileSystem();
        $root = $fileSys->path('/root');
        $cacher = new TestCacher($root, 10);

        $cacher->setCallback($this->callbackVersionEq);

        // Store an atom.
        $cacher->store('aKey', 'aVersion', 'oldContent: don\'t fetch');

        // Test
        // No cache atom there.
        $found = $cacher->loadCached('aKey', 'anotherVersion');

        $this->assertFalse($found);
    }
}
