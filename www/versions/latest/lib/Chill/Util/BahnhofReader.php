<?php
/**
 * Used to read lines from https://opentransportdata.swiss/de/cookbook/bahnhofsliste/
 *
 * @category Util
 * @package  Chill
 * @author   Felice Serena <felice@serena-mueller.ch>
 * @license  MIT License
 */
namespace Chill\Util;

class BahnhofReader {

    public function readFile($filePath) {
        $file = file($filePath);

        $enc = mb_detect_encoding($file[0]);

        $stations = array();
        foreach($file as $line) {
            $line = mb_convert_encoding($line, 'UTF-8', $enc);

            $stats = $this->readLine($line);

            foreach($stats as $s) {
                $stations[] = $s;
            }
        }

        // remove duplicates
        $stations = array_unique($stations, SORT_REGULAR);

        // sort alphabetically
        usort($stations, function($a, $b){
            if($a['stopPointName'] === $b['stopPointName']) {
                return 0;
            }
            return $a['stopPointName'] < $b['stopPointName'] ? -1 : 1;
        });
        return $stations;
    }

    public function readLine($line) {
        $out = array();
        $row = str_getcsv($line);
        $refId = $row[0];

        $names = $this->readNamesStr($row[1]);

        foreach($names as $name) {
            $out[] = array('stopPointRef' => $refId, 'stopPointName' => $name);
        }
        return $out;
    }

    public function readNamesStr($namesStr){
        $namesRaw = explode('$', $namesStr);
        $names = array();

        $lastName = null;
            // non-terminated entries (like the header) are skipped
        foreach($namesRaw as $name){
            switch($name){
            case "<1>":
            case "<2>":
            case "<4>":
                $names[] = $lastName;
                break;
            case "<3>":
                // ignore
                break;
            default:
                $lastName = $name;
            }
        }

        // reduce duplactes as soon as possible
        $names = array_unique($names);

        return $names;
    }
}
