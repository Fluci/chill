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
 * Fetches data from mock, cache or opentransportdata for timetable.
 */
class DataFetcher
{

    /**
     * Highest priority, if set, the mock will be fetched from this path.
     * @var string
     */
    private $mockPath = null;
    /**
     * Second priority, if there's no mock, cache is tried.
     * @var \Chill\Timetable\RequestCacher
     */
    private $cacher = null;
    /**
     * Get new data if nothing found in mock and cache.
     * @var \Chill\Timetable\RequestCreator
     */
    private $reqCreator = null;


    /**
     * Set path to mock file. If found, data will be fetched from mock.
     * @param string $mockPath Path.
     * @return self For chaining.
     */
    public function setMockPath($mockPath)
    {
        $this->mockPath = $mockPath;
        return $this;
    }

    /**
     * Set cacher. Used if no mock is set.
     * @param \Chill\Timetable\Cacher $cacher Access to cache system.
     * @return self For chaining.
     */
    public function setCacher($cacher)
    {
        $this->cacher = $cacher;
        return $this;
    }

    /**
     * Set request creator. It provides all information to create a valid request.
     * @param \Chill\Timetable\RequestCreator $reqCreator Provides methods to
     * create xml request.
     * @return self For chaining.
     */
    public function setRequestCreator($reqCreator)
    {
        $this->reqCreator = $reqCreator;
        return $this;
    }

    /**
     * Fetch data using curl.
     * @param  string $stopPointRef Id of observed station.
     * @return mixed     Result if success, `false` if there's a failure.
     */
    private function fetchCurl($stopPointRef)
    {
        $chnl = curl_init();
        curl_setopt($chnl, CURLOPT_URL, $this->reqCreator->getApiUrl());
        curl_setopt($chnl, CURLOPT_POST, 1);
        curl_setopt($chnl, CURLOPT_RETURNTRANSFER, true);

        $header = $this->reqCreator->getHeader();
        curl_setopt($chnl, CURLOPT_HTTPHEADER, $header);

        $requestBody = $this->reqCreator->getRequestBody($stopPointRef);
        curl_setopt($chnl, CURLOPT_POSTFIELDS, $requestBody);

        $result = curl_exec($chnl);
        curl_close($chnl);
        if ($result === false) {
            error_log("Curl could not fetch request for timetable.");
            return false;
        }

        if ($this->cacher !== null) {
            $noResults = $this->reqCreator->getNumberOfResults();
            $this->cacher->store($stopPointRef, $noResults, $result);
        }

        return $result;
    }

    /**
     * Fetch data using file_get_contents.
     * @param  string $stopPointRef Id of station.
     * @return mixed  `false`if an error occurs, the result otherwise.
     */
    private function fetchFgetContents($stopPointRef)
    {
        if (ini_get('allow_url_fopen') !== "1") {
            error_log(
                'allow_url_fopen has value "'.ini_get('allow_url_fopen')
                .'. Must be "1". file_get_contents will probably fail.'
            );
        }

        $options = $this->reqCreator->getContextOptions($stopPointRef);
        $context = stream_context_create($options);
        $result  = file_get_contents(
            $this->reqCreator->getApiUrl(),
            false,
            $context
        );
        if ($result === false) {
            error_log("file_get_contents could not fetch request for timetable.");
            return false;
        }

        if ($this->cacher !== null) {
            $noResults = $this->reqCreator->getNumberOfResults();
            $this->cacher->store($stopPointRef, $noResults, $result);
        }

        return $result;
    }

    /**
     * Fetches data from every source it can: First mock, then cache, then remote.
     * @param  string $stopPointRef Id of observed station.
     * @return mixed `fetch()` returns `false` if an error occurs, otherwise
     * the fetched result string.
     */
    public function fetch($stopPointRef)
    {
        $result = @file_get_contents($this->mockPath);
        if ($result !== false) {
            return $result;
        }

        $noResults = $this->reqCreator->getNumberOfResults();
        $this->cacher->loadCached($stopPointRef, $noResults);
        if ($result !== false) {
            return $result;
        }

        // No cache, need to request data.
        $result = $this->fetchCurl($stopPointRef);
        if ($result !== false) {
            return $result;
        }

        // Last hope.
        return $this->fetchFgetContents($stopPointRef);
    }
}
