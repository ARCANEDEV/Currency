<?php namespace Arcanedev\Currency\Services\Entities;

use Arcanedev\Currency\Services\Exceptions\InvalidIsoException;
use Arcanedev\Currency\Services\Exceptions\InvalidTypeException;
use Arcanedev\Currency\Services\Exceptions\NotFoundIsoException;

class Currency
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /** @var string */
    protected $iso;

    /** @var int */
    protected $numericIso;

    /** @var string */
    protected $name;

    /** @var string */
    protected $symbol;

    /** @var array */
    protected $altSymbols = [];

    /** @var string */
    protected $subunit;

    /** @var int */
    protected $subunitToUnit;

    /** @var bool */
    protected $symbolFirst;

    /** @var string */
    protected $htmlEntity;

    /** @var string */
    protected $decimalMark;

    /** @var string */
    protected $thousandsMark;

    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
     */
    public function __construct($iso = '')
    {
        if ( ! empty($iso) ) {
            $this->load($iso);
        }
    }

    /**
     * @param string $iso
     *
     * @return Currency
     */
    public function load($iso)
    {
        $this->setIso($iso);

        return $this->loadCurrency();
    }

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @return string
     */
    public function getIso()
    {
        return $this->iso;
    }

    /**
     * @param string $iso
     *
     * @return Currency
     */
    public function setIso($iso)
    {
        $this->checkIso($iso);

        $this->iso = $iso;

        return $this;
    }

    /**
     * @return int
     */
    public function getNumericIso()
    {
        return $this->numericIso;
    }

    /**
     * @param string|int $iso_numeric
     *
     * @throws InvalidTypeException
     *
     * @return Currency
     */
    public function setNumericIso($iso_numeric)
    {
        $this->checkNumericIso($iso_numeric);

        $this->numericIso = $iso_numeric;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Currency
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * @param string $symbol
     *
     * @return Currency
     */
    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;

        return $this;
    }

    /**
     * @return array
     */
    public function getAltSymbols()
    {
        return $this->altSymbols;
    }

    /**
     * @param array $alternate_symbols
     *
     * @return Currency
     */
    public function setAltSymbols($alternate_symbols)
    {
        $this->altSymbols = $alternate_symbols;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubunit()
    {
        return $this->subunit;
    }

    /**
     * @param string $subunit
     *
     * @return Currency
     */
    public function setSubunit($subunit)
    {
        $this->subunit = $subunit;

        return $this;
    }

    /**
     * @return int
     */
    public function getSubunitToUnit()
    {
        return $this->subunitToUnit;
    }

    /**
     * @param int $subunit_to_unit
     *
     * @return Currency
     */
    public function setSubunitToUnit($subunit_to_unit)
    {
        $this->subunitToUnit = $subunit_to_unit;

        return $this;
    }

    /**
     * @return string
     */
    public function getHtmlEntity()
    {
        return $this->htmlEntity;
    }

    /**
     * @param string $html_entity
     *
     * @return Currency
     */
    public function setHtmlEntity($html_entity)
    {
        $this->htmlEntity = $html_entity;

        return $this;
    }

    /**
     * @return Currency
     */
    public function setSymbolFirst()
    {
        return $this->toggleSymbolPosition(true);
    }

    /**
     * @return Currency
     */
    public function setSymbolLast()
    {
        return $this->toggleSymbolPosition(false);
    }

    /**
     * Set Symbol position
     *
     * @param bool $first
     *
     * @throws InvalidTypeException
     *
     * @return Currency
     */
    public function toggleSymbolPosition($first = true)
    {
        $this->symbolFirst = filter_var($first, FILTER_VALIDATE_BOOLEAN);

        return $this;
    }

    /**
     * @return string
     */
    public function getDecimalMark()
    {
        return $this->decimalMark;
    }

    /**
     * @param string $decimal_mark
     *
     * @return Currency
     */
    public function setDecimalMark($decimal_mark)
    {
        $this->decimalMark = $decimal_mark;

        return $this;
    }

    /**
     * @return string
     */
    public function getThousandsMark()
    {
        return $this->thousandsMark;
    }

    /**
     * @param string $thousands_separator
     *
     * @return Currency
     */
    public function setThousandsMark($thousands_separator)
    {
        $this->thousandsMark = $thousands_separator;

        return $this;
    }

    /**
     * @param array $currencyArray
     *
     * @return Currency
     */
    public function setFromArray($currencyArray)
    {
        if ( isset($currencyArray['iso']) ) {
            $this->setIso($currencyArray['iso']);
        }

        $this->setNumericIso($currencyArray['iso_numeric'])
            ->setName($currencyArray['name'])
            ->setSymbol($currencyArray['symbol'])
            ->setAltSymbols($currencyArray['alternate_symbols'])
            ->setSubunit($currencyArray['subunit'])
            ->setSubunitToUnit($currencyArray['subunit_to_unit'])
            ->toggleSymbolPosition($currencyArray['symbol_first'])
            ->setHtmlEntity($currencyArray['html_entity'])
            ->setDecimalMark($currencyArray['decimal_mark'])
            ->setThousandsMark($currencyArray['thousands_separator']);

        return $this;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @param string $iso
     *
     * @return Currency
     */
    public static function make($iso)
    {
        return new self($iso);
    }

    /**
     * @param int|float $amount
     * @param bool      $addSymbol
     *
     * @throws InvalidTypeException
     *
     * @return string
     */
    public function format($amount = 1, $addSymbol = false)
    {
        $this->checkAmount($amount);

        $amount = number_format($amount, 2, $this->getDecimalMark(), $this->getThousandsMark());

        if ( ! $addSymbol ) {
            return $amount;
        }

        return $this->isSymbolFirst()
            ? $this->getSymbol() . ' ' . $amount
            : $amount . ' ' . $this->getSymbol();
    }

    /**
     * @param string $symbol
     *
     * @throws InvalidTypeException
     *
     * @return Currency
     */
    public function addAltSymbol($symbol)
    {
        if ( ! is_string($symbol) ) {
            throw new InvalidTypeException('The alt symbol must be string, '. gettype($symbol) . ' is given !');
        }

        if ( ! in_array($symbol, $this->altSymbols) ) {
            $this->altSymbols[] = $symbol;
        }

        return $this;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Load Functions
     | ------------------------------------------------------------------------------------------------
     */
    protected function loadCurrency()
    {
        $iso                = $this->getIso();

        $currency           = $this->getOneFromConfig($iso);

        $this->setFromArray($currency);

        return $this;
    }

    /**
     * @param string $iso
     * @param array  $currency
     *
     * @return Currency
     */
    public static function loadFromArray($iso, array $currency)
    {
        return self::make($iso)->setFromArray($currency);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Check Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @return boolean
     */
    public function isSymbolFirst()
    {
        return $this->symbolFirst;
    }

    /**
     * @param string $iso
     *
     * @throws InvalidTypeException
     * @throws InvalidIsoException
     * @throws NotFoundIsoException
     */
    protected function checkIso(&$iso)
    {
        if ( ! is_string($iso) ) {
            throw new InvalidTypeException("The ISO must be a string, " . gettype($iso) . " was given.");
        }

        $iso = strtoupper(trim($iso));

        if ( strlen($iso) != 3 ) {
            throw new InvalidIsoException("Invalid ISO name, must contains 3 letters.");
        }

        if ( ! $this->isExists($iso) ) {
            throw new NotFoundIsoException("Invalid ISO name, [$iso] not found.");
        }
    }

    /**
     * @param string|int $iso_numeric
     *
     * @throws InvalidTypeException
     */
    private function checkNumericIso(&$iso_numeric)
    {
        if ( ! is_integer($iso_numeric) and is_string($iso_numeric) and ! ctype_digit($iso_numeric) ) {
            throw new InvalidTypeException('The ISO Number must be an integer value, '. gettype($iso_numeric) . ' is given !');
        }

        $iso_numeric = (int) $iso_numeric;
    }

    /**
     * @param $amount
     *
     * @throws InvalidTypeException
     */
    private function checkAmount(&$amount)
    {
        if ( ! is_numeric($amount) ) {
            throw new InvalidTypeException('The amount must be a numeric value.');
        }

        $amount = round($amount, 2, PHP_ROUND_HALF_UP);
    }

    /**
     * @param string $iso
     *
     * @return bool
     */
    protected function isExists($iso)
    {
        return ! is_null($this->getOneFromConfig($iso));
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @param string $key
     *
     * @return array
     */
    protected function getOneFromConfig($key)
    {
        $key = strtolower($key);

        return array_get(get_currencies(), 'iso.' . $key, null);
    }
}
