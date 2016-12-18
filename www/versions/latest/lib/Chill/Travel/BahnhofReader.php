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
 * Used to read lines from
 * https://opentransportdata.swiss/de/cookbook/bahnhofsliste/
 *
 * @category Util
 * @package  Chill
 */
class BahnhofReader
{


    /**
     * Takes a filePath to a csv and reads the file into an array of station array,
     * they have a field `stopPointName` and a field `stopPointRef`.
     * It denormalizes the data (<1>, <2>, and <4>, see opentransport docs).
     * @param  string $filePath [description]
     * @return array            Array of stations. Returns an empty array
     *                          if an error occurs.
     */
    public function readFile($filePath)
    {
        $file = file($filePath);
        if (empty($file) === true) {
            return array();
        }

        $enc = mb_detect_encoding($file[0]);

        $stations = array();
        foreach ($file as $line) {
            $line = mb_convert_encoding($line, 'UTF-8', $enc);

            $stats = $this->readLine($line);

            foreach ($stats as $s) {
                $stations[] = $s;
            }
        }

        // Remove duplicates
        $stations = array_unique($stations, SORT_REGULAR);

        // Sort alphabetically
        $comparator = function ($lhs, $rhs) {
            if ($lhs['stopPointName'] === $rhs['stopPointName']) {
                return 0;
            }

            if ($lhs['stopPointName'] < $rhs['stopPointName']) {
                return -1;
            }

            return 1;
        };
        usort($stations, $comparator);
        return $stations;
    }

    /**
     * Reads a csv line and splits it up according to <1,2,4>, ignoring <3>.
     * Returns the denormalized array.
     * @param  string $line CSV-line
     * @return array       Denormalized array.
     */
    public function readLine($line)
    {
        $out   = array();
        $row   = str_getcsv($line);
        $refId = $row[0];

        $names = $this->readNamesStr($row[1]);

        foreach ($names as $name) {
            $out[] = array(
                'stopPointRef' => $refId,
                'stopPointName' => $name
            );
        }

        return $out;
    }

    /**
     * Splits the name part of a csv line, returns array of names except for
     * the abbreviation portion.
     * @param  string $namesStr Line in format like "Name one<1>Alternative<2>".
     * @return array
     */
    public function readNamesStr($namesStr)
    {
        $namesRaw = explode('$', $namesStr);
        $names    = array();

        $lastName = null;

        // Non-terminated entries (like the header) are skipped
        foreach ($namesRaw as $name) {
            switch ($name) {
                case "<1>":
                case "<2>":
                case "<4>":
                    $names[] = $lastName;
                    break;
                case "<3>":
                    // Ignore
                    break;
                default:
                    $lastName = $name;
            }
        }

        // Reduce duplicates as soon as possible
        $names = array_unique($names);

        return $names;
    }
}
