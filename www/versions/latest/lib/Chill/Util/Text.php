<?php
/**
 * Models a station halt (when, where, etc.)
 *
 * @category Util
 * @package  Chill
 * @author   Felice Serena <felice@serena-mueller.ch>
 * @license  MIT License
 */
namespace Chill\Util;

class Text
{
    private $text = null;
    private $language = null;

    public function __construct($text, $language)
    {
        $this->text = $text;
        $this->language = $language;
    }

    public function getText()
    {
        return $this->text;
    }

    public function getLanguage()
    {
        return $this->language;
    }
}
