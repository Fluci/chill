<?php
/**
 * Collection of useful functions.
 *
 * @category Util
 * @package  Chill
 * @author   Felice Serena <felice@serena-mueller.ch>
 * @license  MIT License
 */
namespace Chill\Util;

class Util {

	public static function methodRequest($key, $methods = array('COOKIE', 'POST', 'GET'), $default = '') {
		foreach($methods as $method){
			if($method == 'POST' && isset($_POST[$key])){return $_POST[$key];}
			elseif($method == 'GET' && isset($_GET[$key])){return $_GET[$key];}
			elseif($method == 'REQUEST' && isset($_REQUEST[$key])){return $_REQUEST[$key];}
			elseif($method == 'COOKIE' && isset($_COOKIE[$key])){return $_COOKIE[$key];}
		}
		return $default;
	}

	public static function formatZulu($timestamp = null){
		if($timestamp == null){
			$timestamp = time();
		}
		return gmdate('Y-m-d\TH:i:s\Z', $timestamp);
	}

}