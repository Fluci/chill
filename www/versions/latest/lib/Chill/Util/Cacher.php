<?php
/**
 * PHP version 5
 *
 * @category Util
 * @package  Chill
 * @author   Felice Serena <felice@serena-mueller.ch>
 * @license  http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Chill\Util;
/**
 * A cacher is intended to provide an easy mechanism for caching data in
 * a key-value store. Each value (often payload) must have a version.
 * An extending class needs to implement the abstract function `cacheAtomCompare`.
 * It is invoked when the object needs to decide whether an existing cache can
 * be used. Amongst others, the version is passed as part of the CacheAtom to
 * facilitate decision making.
 *
 * @category Util
 * @package  Chill
 */
abstract class Cacher
{
    /*
        The root is the root directory owned by this cacher. Owning a directory
        means, the cacher can do everything, even delete the entire directory.
        For every cache item with key `$key`, it creates a directory in ``$root`.
        In there, every version/cache atom gets stored as a file `$version.txt`.
    */

    /**
     * Directory owned by cacher to store data.
     * @var string
     */
    private $root;

    /**
     * Time interval until cache is invalidated. Unit: seconds.
     * @var int
     */
    private $timeout;

    /**
     * Timeout in seconds.
     * If the cacher encounters a file
     * older than this value, it can choose to check the entire directory for
     * old files and do a general clean up. Default is about a day.
     * [s]
     * @var integer
     */
    public $cleanUpTimeout = 100000;


    /**
     * Creates new cacher that owns the directory at `$cacheRoot`, this means
     * the Cacher should have read/write access. It is allowed to do everything
     * with the directory, for example deleting it whenever it intends to.
     * @param string  $cacheRoot Directory owned by cacher.
     * @param integer $timeout   Timeout in seconds after which
     * a cache item is invalid and will be deleted.
     */
    public function __construct($cacheRoot, $timeout = PHP_INT_MAX)
    {
        $this->root    = $cacheRoot;
        $this->timeout = $timeout;
    }

    /**
     * Suffix of a cache atom file. With leading dot.
     * @return string
     */
    public function atomFileSuffix()
    {
        return '.txt';
    }

    /**
     * Decides if there exists a suitable cached file with `$key` and `$version`.
     * It repeatedly calls cacheAtomCompare to decide this.
     * `$key` and `$version` must be valid path components.
     * @param  string $key     Unique identifier to get cache resource.
     * @param  string $version Decides if a cached file can be used.
     * @return mixed           `false` if no suitable file exists.
     */
    public function loadCached($key, $version)
    {
        $this->checkPathComponent($key);
        $this->checkPathComponent($version);

        $keyDir  = $this->keyPath($key);
        $entries = $this->dirEntries($keyDir);
        if ($entries === false) {
            return false;
        }

        $new    = new CacheAtom($version);
        $now    = time();
        $oldest = -1;
        $result = false;
        foreach ($entries as $cacheAtom) {
            if ($cacheAtom === '.' || $cacheAtom === '..') {
                continue;
            }

            $atomPath = $keyDir.DIRECTORY_SEPARATOR.$cacheAtom;
            $old      = $this->createAtom($atomPath);
            $oldest   = max($oldest, $now - $old->getLastModified());
            if ($this->atomIsValid($old) === true
                && $this->cacheAtomCompare($new, $old) === true
            ) {
                $cache = file_get_contents($atomPath);
                if ($cache !== false) {
                    // Success, got valid cache, no race condition encountered.
                    $result = $cache;
                    break;
                }
            }
        }

        $this->cleanIfOld($oldest);
        return $result;
    }

    /**
     * Write $payload to cache.
     * `$key` and `$path` must be valid path components.
     * @param  string $key     Cache item key.
     * @param  string $version Version string of cache atom.
     * @param  string $payload String to store in cache.
     * @return void
     */
    public function store($key, $version, $payload)
    {
        $this->checkPathComponent($key);
        $this->checkPathComponent($version);

        $keyDir = $this->keyPath($key);
        if (file_exists($keyDir) === false) {
            mkdir($keyDir, 0777, true);
        }

        $atomPath = $this->atomPath($key, $version);
        // Get exclusive write access with LOCK_EX.
        $res = @file_put_contents($atomPath, $payload, LOCK_EX);
        // In case the file system doesn't support locks, we go
        // for the race condition variant (mainly for unit-tests).
        if ($res === false) {
            file_put_contents($atomPath, $payload);
            error_log("Cacher could not write atom file atomically.");
        }
    }

    /**
     * Takes two cache atoms describing potential cache files.
     * If `$old` exists, it is a valid cache atom.
     * @param  \Chill\Util\CacheAtom $new Cache atom of new file to create.
     * @param  \Chill\Util\CacheAtom $old Cache atom of already existing file.
     * @return bool                  True if `$old` is good enough to
     *                               be delivered as `$new`.
     */
    abstract protected function cacheAtomCompare(
        \Chill\Util\CacheAtom $new,
        \Chill\Util\CacheAtom $old = null
    );

    /*
        ***** Clean up of directory, Garbage Collection *****
    */


    /**
     * Internal lazy clean up method. Cleans directory here and there depending
     * on cleanUpTimeout.
     * @param  integer $age How old is the encountered file? [s]
     * @return void
     */
    private function cleanIfOld($age)
    {
        if ($age <= $this->cleanUpTimeout) {
            return;
        }

        $this->cleanCache();
    }

    /**
     * Garbage collection: Deletes everything that is invalidated.
     * Does not delete empty directories.
     * @return void
     */
    public function cleanCache()
    {
        $entries = $this->dirEntries($this->root);
        if ($entries === false) {
            return;
        }

        foreach ($entries as $cacheDir) {
            if ($cacheDir === '.' || $cacheDir === '..') {
                continue;
            }

            $cacheDirPath = $this->root.DIRECTORY_SEPARATOR.$cacheDir;

            if (false !== is_dir($cacheDirPath)) {
                $this->cleanCacheDir($cacheDirPath);
            }
        }
    }

    /**
     * Clean cache for a key: Removes old caches of a cache item
     * but keeps recent ones.
     * Does not delete any directories.
     * @param  string $key Key of cache item.
     * @return void
     */
    public function cleanKey($key)
    {
        $this->checkPathComponent($key);
        $path = $this->keyPath($key);
        if (false !== is_dir($path)) {
            $this->cleanCacheDir($path);
        }
    }

    /**
     * Garbage collection: Deletes every file in the cache-key directory if invalid.
     * Does not delete the directory.
     * @param  string $cacheDir Cache directory of a key to clean.
     * @return void
     */
    private function cleanCacheDir($cacheDir)
    {
        if (file_exists($cacheDir) === false) {
            return;
        }

        $entries = $this->dirEntries($cacheDir);
        if ($entries === false) {
            return;
        }

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $entryPath = $cacheDir.DIRECTORY_SEPARATOR.$entry;
            if (false !== is_file($entryPath)) {
                $cacheAtom = $this->createAtom($entryPath);
                $this->cleanAtom($entryPath, $cacheAtom);
            }
        }
    }

    /**
     * Decides if a cache on it's own is still valid by looking at its cache atom.
     * @param  \Chill\Util\CacheAtom $cacheAtom Atom belonging to a cache value.
     * @return bool           True if the atom on it's own is valid.
     */
    protected function atomIsValid(\Chill\Util\CacheAtom $cacheAtom)
    {
        return time() - $cacheAtom->getLastModified() <= $this->timeout;
    }

    /**
     * Has the choice to delete the given atom file.
     * @param  string                $atomPath  Full path to file.
     * @param  \Chill\Util\CacheAtom $cacheAtom CacheAtom of file.
     * @return bool      True if file deleted, false if it is still valid.
     */
    protected function cleanAtom($atomPath, \Chill\Util\CacheAtom $cacheAtom)
    {
        if ($this->atomIsValid($cacheAtom) === true) {
            return false;
        }

        // Ignore errors, since another process could have deleted it already.
        @unlink($atomPath);
        return true;
    }


    /*
        ***** Helper methods *****
    */


    /**
     * Convenience method to create a CacheAtom from a file path.
     * @param  string $filePath Path to an existing file.
     * @return mixed       \Chill\Util\CacheAtom if it succeeds.
     * If there's an error, `false` is returned.
     */
    private function createAtom($filePath)
    {
        $lastMod = filemtime($filePath);
        if ($lastMod === false) {
            return false;
        }

        $version = basename($filePath, $this->atomFileSuffix());
        return new CacheAtom($version, $lastMod);
    }

    /**
     * Knows how to create the path to a cache directory where everything associated
     * with the `$key` of the cache item is stored.
     * @param  string $key Key of cache item.
     * @return string      Path to cache directory for `$key`.
     */
    private function keyPath($key)
    {
        return $this->root.DIRECTORY_SEPARATOR.$key;
    }

    /**
     * Creates the file path to the file that stores the payload for the key `$key`
     * of version `$version`.
     * @param  string $key     Key of cache resource.
     * @param  string $version Version of cache resource.
     * @return string          Path to file that stores payload.
     */
    private function atomPath($key, $version)
    {
        $psep = DIRECTORY_SEPARATOR;
        return $this->root.$psep.$key.$psep.$version.$this->atomFileSuffix();
    }

    /*
        ***** General Filesystem helpers ignorant of cacher *****
    */


    /**
     * Symbols that can mess up a file path and hence should not apear in a
     * path component.
     * @return array Array of characters that mess up paths
     */
    public function getNastyPathSymbols()
    {
        static $nps = array(
                       '/',
                       '\\',
            );
        return $nps;
    }

    /**
     * Checks if the passed string could be used as path compontent.
     * A path component is part of a valid direcotry/file-path and contains no
     * directory separators (back-/slashes).
     * @param  string $pathComponent Candidate for being a path component.
     * @return void
     */
    public function validatePathComponent($pathComponent)
    {
        foreach ($this->getNastyPathSymbols() as $needle) {
            if (false !== strpos($pathComponent, $needle)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validates path component. If it isn't a valid path component, an exception
     * is thrown.
     * @param  string $pathComponent Part between slashes in a path.
     * @return void
     */
    protected function checkPathComponent($pathComponent)
    {
        if (true === empty($pathComponent)
            || false === $this->validatePathComponent($pathComponent)
        ) {
            throw new Exception("Invalid path part encountered: ".$pathComponent);
        }
    }

    /**
     * Expects path to a directory. Returns an array of all found directory
     * entries.
     * @param  string $dir Path of directory.
     * @return mixed       If no valid path is given, the directory
     * doesn't exist or some other error occurs, false is returned.
     * In case of success, `dirEntries()` returns an array of all directory entries.
     */
    private function dirEntries($dir)
    {
        $handle = @opendir($dir);
        if (false === $handle) {
            return false;
        }

        $entries = array();
        while (false !== ($entry = readdir($handle))) {
            $entries[] = $entry;
        }

        closedir($handle);
        return $entries;
    }
}
