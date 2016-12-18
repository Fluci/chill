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
 * Collection of useful functions.
 *
 * @category Util
 * @package  Chill
 */
class Util
{


    /**
     * Easy wrapper to acces $_POST, $_GET, $COOKIE, etc.
     * @param  string $key     Key to search for.
     * @param  array  $methods Priority of different super globals.
     * @param  misc   $default Value to return if `$key` can't be found.
     * @return misc   Found value, maybe `$default`.
     */
    public static function methodRequest(
        $key,
        $methods = array('COOKIE', 'POST', 'GET'),
        $default = ''
    ) {
        foreach ($methods as $method) {
            if ($method === 'POST' && isset($_POST[$key]) === true) {
                return $_POST[$key];
            }

            if ($method === 'GET' && isset($_GET[$key]) === true) {
                return $_GET[$key];
            }

            if ($method === 'REQUEST' && isset($_REQUEST[$key]) === true) {
                return $_REQUEST[$key];
            }

            if ($method === 'COOKIE' && isset($_COOKIE[$key]) === true) {
                return $_COOKIE[$key];
            }
        }

        return $default;
    }

    /**
     * Format unix timestamp in Zulu time.
     * @param  int $timestamp Unix timestamp.
     * @return string
     */
    public static function formatZulu($timestamp = null)
    {
        if ($timestamp === null) {
            $timestamp = time();
        }

        return gmdate('Y-m-d\TH:i:s\Z', $timestamp);
    }
}
