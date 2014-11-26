<?php namespace Arcanedev\Currency\Services\Providers;

use Arcanedev\Currency\Contracts\CurrencyProvider;
use Arcanedev\Currency\Helpers\CURL;
use Arcanedev\Currency\Services\Entities\Rate;

class FreeCurrencyConverterApi implements CurrencyProvider
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    protected $baseUrl = 'http://www.freecurrencyconverterapi.com/api';

    protected $version = 'v2';

    protected $query = [];

    /** @var CURL */
    protected $client;

    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
     */
    function __construct()
    {
        $this->query    = [];
        $this->client   = new CURL;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    private function getQuery()
    {
        return '?q=' . implode(',', $this->query);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @param string $endpoint
     *
     * @return string
     */
    protected function prepareUrl($endpoint = '')
    {
        return implode('/', [$this->baseUrl, $this->version, $endpoint]);
    }

    /**
     * @param string $endpoint
     * @param bool $compact
     *
     * @return string
     */
    private function prepareQuery($endpoint, $compact)
    {
        $url = $this->prepareUrl($endpoint) . $this->getQuery();
        if ( $compact )
            $url .= '&compact=y';

        return $url;
    }

    public function getCurrenciesKey($from, $to)
    {
        return strtoupper($from . '_' . $to);
    }

    /**
     * @param string $from
     * @param string $to
     */
    public function addConverter($from, $to)
    {
        $this->query[] = $this->getCurrenciesKey($from, $to);
    }

    public function getAll()
    {
        $url    = $this->prepareUrl('currencies');
        $result = $this->client->sendRequest($url);

        return json_decode($result, true);
    }

    /**
     * @param string $from
     * @param string $to
     * @param bool   $compact
     *
     * @return mixed
     */
    public function convert($from, $to, $compact = true)
    {
        $this->addConverter($from, $to);

        $url    = $this->prepareQuery('convert', $compact);

        $result = $this->getResult($url);

        $key    = $this->getCurrenciesKey($from, $to);

        $ratio  = (new Rate())
            ->setIsoFrom($from)
            ->setIsoTo($to)
            ->setRatio($result[$key]['val']);

        return $ratio;
    }


    /**
     * @param array $currencies
     * @param bool  $compact
     *
     * @return mixed
     */
    public function convertMany(array $currencies = [], $compact = true)
    {
        foreach ($currencies as $from => $toCurrencies) {
            foreach ($toCurrencies as $to) {
                $this->addConverter($from, $to);
            }
        }

        $url    = $this->prepareQuery('convert', $compact);

        return $this->getResult($url);
    }

    /**
     * @param $url
     *
     * @return mixed
     */
    private function getResult($url)
    {
        $result = $this->client->sendRequest($url);

        $result = json_decode($result, true);

        return $this->getResultEntity($result);
    }

    /**
     * @param array $result
     *
     * @return array
     */
    private function getResultEntity($result)
    {
        $entity = [];

        foreach($result as $key => $rate) {
            list($from, $to) = explode('_', $key);
            $entity[$from][$to] = $rate['val'];
        }

        return $entity;
    }

    /**
     * @param string $from
     * @param string $to
     * @param float $amount
     *
     * @return float
     */
    public function calculate($from, $to, $amount)
    {
        return $this->convert($from, $to)->calculate($amount);
    }
}
