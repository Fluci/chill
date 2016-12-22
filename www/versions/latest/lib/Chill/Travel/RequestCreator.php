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
 * Abstract request generator for interaction with opentransportdata.swiss.
 * Provides all "static" (not frequently changing) data to generate a request.
 *
 * Get an API key from
 * https://opentransportdata.swiss/de/dev-dashboard/
 *
 * @category Travel
 * @package  Chill
 */
abstract class RequestCreator
{
    private $apiUrl = null;
    private $apiKey = null;


    /**
     * Initialize RequestReader with apiKey.
     * @param string $apiUrl API url to access opentransportdata
     * @param string $apiKey API key to access opentransportdata
     */
    public function __construct($apiUrl, $apiKey)
    {
        $this->apiUrl = $apiUrl;
        $this->apiKey = $apiKey;
    }

    /**
     * Read the set API url.
     * @return string
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * Set a new API url.
     * @param string $apiUrl Url to acces API of opentransportdata.swiss
     * @return self For chaining.
     */
    public function setApiUrl($apiUrl)
    {
        $this->apiUrl = $apiUrl;
        return $this;
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
