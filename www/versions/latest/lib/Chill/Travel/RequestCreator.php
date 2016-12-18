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
 * Abstract request generator for interaction with opentransportdata.swiss
 *
 * Get an API key from
 * https://opentransportdata.swiss/de/dev-dashboard/
 *
 * @category Travel
 * @package  Chill
 */
abstract class RequestCreator
{
    private $apiKey = null;


    /**
     * Initialize RequestReader with apiKey.
     * @param string $apiKey API key to access opentransportdata
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Read the set API key.
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Set a new API key.
     * @param string $apiKey Key for opentransportdata.swiss
     * @return self For chaining.
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }
}
