<?php namespace Arcanedev\Currency\Services\Providers;

use Arcanedev\Currency\Contracts\CurrencyProvider as CurrencyProvider;

class YahooFinance extends BaseProvider implements CurrencyProvider
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /** @var string */
    protected $baseUrl  = 'http://query.yahooapis.com';

    /** @var string */
    protected $version  = 'v1';

    /** @var string */
    const YQL_QUERY     = "select * from yahoo.finance.xchange where pair in (:currencies:)";

    /** @var array */
    protected $query = [];

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
    /**
     * @return string
     */
    private function getOtherAttributes()
    {
        return "&env=http://datatables.org/alltables.env&format=json"; // ENV & Format
    }

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @param string $from
     * @param string $to
     * @param bool   $compact
     *
     * @return array
     */
    public function convert($from, $to, $compact = true)
    {
        $this->addCurrency($from, $to);

        $url    = $this->prepareQuery('public/yql', $compact);

        $result = $this->getResult($url);

        return $this->getResultEntity($result);
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

        $url    = $this->prepareQuery('public/yql', $compact);

        $result = $this->getResult($url);

        return $this->getResultEntities($result);
    }

    /**
     * @param string $url
     *
     * @return mixed|null
     */
    private function getResult($url)
    {
        try
        {
            $result = $this->client->sendRequest($url);

            return json_decode($result, true);
        }
        catch (\Exception $e)
        {
            return null;
        }
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @param string $from
     * @param string $to
     *
     * @return string
     */
    protected function formatCurrencyKey($from, $to)
    {
        return '"' . strtoupper($from . $to) . '"';
    }

    /**
     * @param string $endpoint
     * @param bool $compact
     *
     * @return string
     */
    private function prepareQuery($endpoint, $compact = true)
    {
        $url = [
            $this->prepareUrl($endpoint),
            "?q=" . $this->formatQuery(),
            $this->getOtherAttributes(),
        ];

        if ( ! $compact ) {
            $url[] = '&diagnostics=true';
        }

        return implode('', $url);
    }

    /**
     * @param array $result
     *
     * @return array
     */
    private function getResultEntity($result)
    {
        $rate   = $result['query']['results']['rate'];
        $entity = [];

        $this->extractEntity($entity, $rate);

        return $entity;
    }

    /**
     * @param array $result
     *
     * @return array
     */
    private function getResultEntities($result)
    {
        $rates  = $result['query']['results']['rate'];
        $entity = [];

        foreach($rates as $rate) {
            $this->extractEntity($entity, $rate);
        }

        return $entity;
    }

    /**
     * @param $entity
     * @param $rate
     *
     * @return array
     */
    private function extractEntity(&$entity, $rate)
    {
        list($from, $to)    = explode(' to ', $rate['Name']);
        $entity[$from][$to] = (float) $rate['Rate'];

        return $entity;
    }

    /**
     * @return string
     */
    public function formatQuery()
    {
        $currencies = implode(', ', $this->query);

        return $this->parseQuery($currencies);
    }

    /**
     * @param string $currencies
     *
     * @return string
     */
    private function parseQuery($currencies)
    {
        $query = str_replace(':currencies:', $currencies, self::YQL_QUERY);

        return urlencode($query);
    }
}
