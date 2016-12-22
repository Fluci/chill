<?php
/**
 * PHP version 5
 *
 * @category Config
 * @package  Chill
 * @author   Felice Serena <felice@serena-mueller.ch>
 * @license  http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Chill\Timetable;

/**
 * Knows which cache atoms to choose when fetching from cache.
 * @category Config
 * @package  Chill
 */
class RequestCacher extends \Chill\Util\Cacher
{


    /**
     * Takes two cache atoms describing potential cache files.
     * If `$old` exists, it is a valid cache atom.
     * @param  \Chill\Util\CacheAtom $new CacheAtom of new file to create.
     * @param  \Chill\Util\CacheAtom $old CacheAtom of already existing file.
     * @return bool                  True if `$old` is good enough to
     *                               be delivered in the place of `$new`.
     */
    function cacheAtomCompare(
        \Chill\Util\CacheAtom $new,
        \Chill\Util\CacheAtom $old = null
    ) {
        return $old !== null
            && $new->getVersion() <= $old->getVersion();
    }
}
