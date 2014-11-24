<?php namespace Arcanedev\Currency\Services\Entities;

use Arcanedev\Currency\Services\Exceptions\InvalidTypeException;

class Rate
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /** @var Currency */
    protected $from;

    /** @var Currency */
    protected $to;

    /** @var float */
    protected $exchangeRate;

    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
     */
    public function __construct()
    {
        $this->from = new Currency;
        $this->to   = new Currency;
        $this->setExchangeRate(0);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @param float $rate
     *
     * @throws InvalidTypeException
     *
     * @return Rate
     */
    public function setExchangeRate($rate)
    {
        $this->checkExchangeRate($rate);

        $this->exchangeRate = $rate;

        return $this;
    }

    /**
     * @return Currency
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param string $from
     *
     * @return Rate
     */
    public function setIsoFrom($from)
    {
        $from = $this->makeCurrency($from);

        return $this->setFrom($from);
    }

    /**
     * @param Currency $from
     *
     * @return Rate
     */
    public function setFrom(Currency $from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @return Currency
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param string $to
     *
     * @return Rate
     */
    public function setIsoTo($to)
    {
        $to = $this->makeCurrency($to);

        return $this->setTo($to);
    }

    /**
     * @param Currency $to
     *
     * @return Rate
     */
    public function setTo($to)
    {
        $this->to   = $to;

        return $this;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @param float|int $amount
     *
     * @param bool      $format
     *
     * @throws InvalidTypeException
     * @throws \Exception
     *
     * @return float
     */
    public function calculate($amount = 1, $format = false)
    {
        $this->checkAmount($amount);

        $amount = round($this->exchangeRate * $amount, 2, PHP_ROUND_HALF_UP);

        if ( $format ) {
            return $this->to->format($amount, true);
        }

        return $amount;
    }

    private function makeCurrency($iso)
    {
        return Currency::make($iso);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Check Functions
     | ------------------------------------------------------------------------------------------------
     */
    public function isSameCurrencies()
    {
        return $this->from->getIso() === $this->to->getIso();
    }

    public function isDifferentCurrencies()
    {
        return ! $this->isSameCurrencies();
    }

    /**
     * @param $amount
     *
     * @throws InvalidTypeException
     * @throws \Exception
     */
    private function checkAmount(&$amount)
    {
        if ( ! is_numeric($amount) ) {
            throw new InvalidTypeException('The exchange rate must be a numeric value.');
        }

        if ( $amount < 0 ) {
            throw new \Exception('The amount must be greater than 0.');
        }

        $amount = (float) $amount;
    }

    /**
     * @param $rate
     *
     * @throws InvalidTypeException
     * @throws \Exception
     */
    private function checkExchangeRate(&$rate)
    {
        if ( ! is_numeric($rate) ) {
            throw new InvalidTypeException('The exchange rate must be a numeric value.');
        }

        if ( $rate < 0 ) {
            throw new \Exception('The exchange rate must be greater than 0.');
        }
    }
}
