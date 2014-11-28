<?php namespace Arcanedev\Currency\Services\Providers;

use Arcanedev\Currency\Helpers\CURL;

abstract class BaseProvider
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /** @var string */
    protected $baseUrl;

    /** @var string */
    protected $version;

    /** @var CURL */
    protected $client;

    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
     */
    public function __construct()
    {
        // TODO: Implement __construct() method.
        $this->query    = [];
        $this->client   = new CURL;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function getVersion()
    {
        return ! is_null($this->version) ? $this->version : '';
    }

    protected function getRateKey($from, $to)
    {
        return strtoupper($from . '_' . $to);
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
    abstract public function convert($from, $to, $compact = true);

    /**
     * @param array $currencies
     * @param bool  $compact
     *
     * @return array
     */
    abstract public function convertMany(array $currencies = [], $compact = true);

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @param array $currencies
     */
    protected function addManyCurrencies(array $currencies)
    {
        foreach ($currencies as $from => $toCurrencies) {
            foreach ($toCurrencies as $to) {
                $this->addCurrency($from, $to);
            }
        }
    }

    /**
     * @param string $from
     * @param string $to
     */
    protected function addCurrency($from, $to)
    {
        $this->query[] = $this->formatCurrencyKey($from, $to);
    }

    /**
     * @param string $from
     * @param string $to
     *
     * @return string
     */
    abstract protected function formatCurrencyKey($from, $to);

    /**
     * @param string $endpoint
     *
     * @return string
     */
    protected function prepareUrl($endpoint = '')
    {
        return implode('/', [
            $this->getBaseUrl(),
            $this->getVersion(),
            $endpoint
        ]);
    }
}
