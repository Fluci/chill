<?php
/**
 * Entry point for website. Checks input and decides on page to display.
 *
 * PHP version 7
 *
 * @category Website
 * @package  Chill
 * @author   Felice Serena <felice@serena-mueller.ch>
 * @license  http://www.opensource.org/licenses/mit-license.html  MIT License
 */

require_once __DIR__.'/../environment.php';

$use_mock;
if (\Chill\Util\Util::methodRequest('mock', array('GET'), '0') === '-1') {
    $use_mock = true;
} else {
    $use_mock = $CONFIG['use_mock'];
}

$PAGE['use_mock'] = $use_mock;

/*
    Important links:

    https://opentransportdata.swiss/dataset/695c7af6-d486-4cde-9bf0-a92fdd581a4e
    /resource/911a4eb7-9d10-440f-904b-b0872b9727c1/download/bahnhof.csv

    https://opentransportdata.swiss/dataset/bhlist
*/

// 8503099
$stopPointRef = \Chill\Util\Util::methodRequest('stop_point_ref', array('GET'), '-1');

if (strlen($stopPointRef) !== 7) {
    $stopPointRef = '-1';
}

$stopPointRefInt = intval($stopPointRef);
if ($stopPointRefInt <= 0 || 9999999 < $stopPointRefInt) {
    $stopPointRef = '-1';
}

$success = true;
if ($stopPointRef !== '-1') {
    $success = require_once VER_INC_ROOT.'/timetable.php';
    if ($success === true) {
        return;
    }
}

if ($success === true) {
    $PAGE['loadingDataFailed'] = false;
    require_once VER_INC_ROOT.'/stations_overview.php';
} else {
    $PAGE['loadingDataFailed'] = true;
    require_once VER_INC_ROOT.'/stations_overview.php';
}
