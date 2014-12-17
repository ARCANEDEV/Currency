<?php namespace Arcanedev\Currency;

use Arcanedev\Currency\Contracts\CurrencyProvider;

use Arcanedev\Currency\Services\Entities\RateCollection;

class Converter
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /** @var CurrencyProvider */
    protected $provider;

    /** @var array */
    protected $currencies   = [];

    /** @var RateCollection */
    protected $result;

    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
     */
    function __construct(CurrencyProvider $provider)
    {
        $this->provider     = $provider;
        $this->currencies   = [];
        $this->result       = new RateCollection;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get the Rate collection result
     *
     * @return RateCollection
     */
    public function getResult()
    {
        return $this->result;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Add From currency with To currencies
     *
     * @param string $fromCurrency
     * @param array  $toCurrencies
     *
     * @return Converter
     */
    public function add($fromCurrency, array $toCurrencies)
    {
        $toCurrencies = array_filter($toCurrencies);

        $this->checkFromCurrency($fromCurrency);

        if ( ! empty($fromCurrency) && ! empty($toCurrencies)) {
            $this->currencies[$fromCurrency] = $toCurrencies;
        }

        return $this;
    }

    /**
     * Add Many Currencies
     *
     * @param array $currencies
     *
     * @return Converter
     */
    public function addMany(array $currencies)
    {
        $currencies = array_filter($currencies);

        foreach ($currencies as $fromCurrency => $toCurrencies) {
            $this->add($fromCurrency, $toCurrencies);
        }

        return $this;
    }

    /**
     * @param array $currencies
     *
     * @return RateCollection
     */
    public function convert(array $currencies = [])
    {
        $currencies = array_filter($currencies);

        if ( empty($currencies) ) {
            return new RateCollection();
        }

        $this->addMany($currencies);

        return $this->retrieveResult();
    }

    /**
     * @return RateCollection
     */
    private function retrieveResult()
    {
        if ( empty($this->currencies) ) {
            return new RateCollection;
        }

        $result = $this->provider->convertMany($this->currencies);

        return $this->result->addMany($result);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Check Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @param string $fromCurrency
     */
    // TODO: Complete checkFromCurrency(&$fromCurrency) implementation
    private function checkFromCurrency(&$fromCurrency)
    {
        if ( ! is_string($fromCurrency) ) {
        }

        $this->checkIsoCurrency($fromCurrency);
    }

    /**
     * @param $toCurrencies
     */
    // TODO: Complete checkToCurrencies(&$toCurrencies) implementation
    private function checkToCurrencies(&$toCurrencies)
    {
        if ( ! is_array($toCurrencies) ) {

        }
    }

    /**
     * @param string $currency
     */
    // TODO: Complete checkIsoCurrency($currency) implementation
    private function checkIsoCurrency($currency)
    {
        $currency = trim($currency);

        if ( strlen($currency) !== 3 ) {

        }
    }
}
