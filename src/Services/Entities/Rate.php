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
        $currency = new Currency;
        $this->setFrom($currency);
        $this->setTo($currency);
        $this->setExchangeRate(0);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @return string
     */
    public function getKey()
    {
        return implode('_', [
            $this->getFromIso(),
            $this->getToIso(),
        ]);
    }

    /**
     * @param string $key
     *
     * @return Rate
     */
    public function setKey($key)
    {
        list($from, $to) = explode('_',$key);

        $this->setIsoFrom($from);
        $this->setIsoTo($to);

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
     * @return string
     */
    public function getFromIso()
    {
        return $this->from->getIso();
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
     * @return string
     */
    public function getToIso()
    {
        return $this->to->getIso();
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
     * @return float
     */
    public function getExchangeRate()
    {
        return $this->exchangeRate;
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

    /**
     * @param string $iso
     *
     * @return Currency
     */
    private function makeCurrency($iso)
    {
        return Currency::make($iso);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Check Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @return bool
     */
    public function isSameCurrencies()
    {
        return $this->from->getIso() === $this->to->getIso();
    }

    /**
     * @return bool
     */
    public function isDifferentCurrencies()
    {
        return ! $this->isSameCurrencies();
    }

    /**
     * @param float $amount
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
     * @param float $rate
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
