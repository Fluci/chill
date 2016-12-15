<?php
/**
 *
 * PHP version 5
 *
 * @category Website
 * @package  Chill
 * @author   Felice Serena <felice@serena-mueller.ch>
 * @license  MIT License
 */

require_once __DIR__.'/../environment.php';

$PAGE['owner'] = $CONFIG['owner'];
$PAGE['nav_active'] = 'about';
$template = $TWIG->loadTemplate('about.html.twig');
$output   = $template->render(array('page' => $PAGE));

echo $output;
