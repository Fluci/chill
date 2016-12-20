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
 * Describes a unique version of a cache item.
 */
class CacheAtom
{
    /**
     * General purpose field: Use for your own version system.
     * @var string
     */
    private $version;

    /**
     * Unix timestamp of last change of cache atom.
     * @var integer
     */
    private $lastModified;


    /**
     * Create a cache atom. A cache atom is the smallest unit of a cache item.
     * It has to carry a non-empty, path-component compatible version string.
     * @param string  $version      Version string defined by client.
     * @param integer $lastModified When was the last modification of this atom?
     *                              How old is the data? Unix timestamp.
     */
    public function __construct($version, $lastModified = null)
    {
        $this->version      = $version;
        $this->lastModified = $lastModified;
        if ($lastModified === null) {
            $this->lastModified = time();
        }
    }

    /**
     * General purpose field: String for user to do version control.
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Unix timestamp of last change.
     * @return integer
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

};
