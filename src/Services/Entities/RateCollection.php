<?php namespace Arcanedev\Currency\Services\Entities;

use Arcanedev\Currency\Support\Collection;

class RateCollection extends Collection
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /** @var array */
    protected $items = [];

    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @param array $items
     */
    function __construct($items = [])
    {
        parent::__construct($items);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    public function getAllIso()
    {
        $isos = [];

        foreach ($this->keys() as $key) {
            list($from, $to) = explode('_', $key);

            if ( ! in_array($from, $isos) ) {
                $isos[] = $from;
            }

            if ( ! in_array($to, $isos) ) {
                $isos[] = $to;
            }
        }

        return $isos;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @param string $key
     * @param float  $exchangeRate
     *
     * @return RateCollection
     */
    public function add($key, $exchangeRate)
    {
        $rate   = new Rate;
        $rate->setKey($key)->setExchangeRate($exchangeRate);

        if ( ! $this->has($key) ) {
            $this->put($key, $rate);
        }

        return $this;
    }

    /**
     * @param array $rates
     *
     * @return RateCollection
     */
    public function addMany(array $rates)
    {
        foreach($rates as $from => $toCurrency) {
            foreach($toCurrency as $to => $exchangeRate) {
                $this->add($this->prepareKey($from, $to), $exchangeRate);
            }
        }

        return $this;
    }

    /**
     * @param string $from
     * @param string $to
     * @param float  $amount
     * @param bool   $format
     *
     * @return float|string
     */
    public function calculate($from, $to, $amount = 1.0, $format = false)
    {
        $key = $this->prepareKey($from, $to);

        return $this->calculateFromKey($key, $amount, $format);
    }

    /**
     * @param string    $key
     * @param float $amount
     * @param bool      $format
     *
     * @return float|string
     */
    public function calculateFromKey($key, $amount = 1.0, $format = false)
    {
        $this->checkAmount($amount);

        if ( $this->has($key) ) {
            /** @var Rate $rate */
            $rate = $this->get($key);

            return $rate->calculate($amount, $format);
        }

        return $amount;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Check Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @param float $amount
     */
    private function checkAmount($amount)
    {
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
    public function prepareKey($from, $to)
    {
        return implode('_', [$from, $to]);
    }
}
