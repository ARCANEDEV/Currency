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
     * @param string $fromCurrency
     * @param array  $toCurrencies
     *
     * @return Converter
     */
    public function add($fromCurrency, array $toCurrencies)
    {
        $this->currencies[$fromCurrency] = $toCurrencies;

        return $this;
    }

    /**
     * @param array $currencies
     *
     * @return Converter
     */
    public function addMany(array $currencies)
    {
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
        if ( ! empty($currencies) ) {
            $this->addMany($currencies);
        }

        return $this->retrieveResult();
    }

    /**
     * @return RateCollection
     */
    private function retrieveResult()
    {
        $result = $this->provider->convertMany($this->currencies);

        return $this->result->addMany($result);
    }
}
