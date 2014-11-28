<?php namespace Arcanedev\Currency\Services\Providers;

use Arcanedev\Currency\Contracts\CurrencyProvider;
use Arcanedev\Currency\Helpers\CURL;
use Arcanedev\Currency\Services\Entities\Rate;

class FreeCurrencyConverterApi extends BaseProvider implements CurrencyProvider
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /** @var string */
    protected $baseUrl = 'http://www.freecurrencyconverterapi.com/api';

    /** @var string */
    protected $version = 'v2';

    /** @var array */
    protected $query = [];

    /** @var CURL */
    protected $client;

    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
     */
    public function __construct()
    {
        parent::__construct();
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
     * @param bool $compact
     *
     * @return string
     */
    private function prepareQuery($endpoint, $compact)
    {
        $url = $this->prepareUrl($endpoint) . $this->getQuery();

        if ( $compact ) {
            $url .= '&compact=y';
        }

        return $url;
    }

    protected function formatCurrencyKey($from, $to)
    {
        return strtoupper(trim($from) . '_' . trim($to));
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
     * @return Rate
     */
    public function convert($from, $to, $compact = true)
    {
        $this->addCurrency($from, $to);

        $url    = $this->prepareQuery('convert', $compact);

        $result = $this->getResult($url);

        $key    = $this->getRateKey($from, $to);

        $rate  = (new Rate())
            ->setIsoFrom($from)
            ->setIsoTo($to)
            ->setExchangeRate($result[$key]['val']);

        return $rate;
    }

    /**
     * @param array $currencies
     * @param bool  $compact
     *
     * @return array
     */
    public function convertMany(array $currencies = [], $compact = true)
    {
        $this->addManyCurrencies($currencies);

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
            list($from, $to)    = explode('_', $key);
            $entity[$from][$to] = $rate['val'];
        }

        return $entity;
    }

    /**
     * @param string $from
     * @param string $to
     * @param float  $amount
     *
     * @return float
     */
    public function calculate($from, $to, $amount)
    {
        return $this->convert($from, $to)->calculate($amount);
    }
}
