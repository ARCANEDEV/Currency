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
    protected $thousandsSeparator;

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
     * @param int $iso_numeric
     *
     * @return Currency
     */
    public function setNumericIso($iso_numeric)
    {
        $this->numericIso = (int) $iso_numeric;

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
     * @param bool $first
     *
     * @return Currency
     */
    public function toggleSymbolPosition($first = true)
    {
        $this->symbolFirst = $first;

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
    public function getThousandsSeparator()
    {
        return $this->thousandsSeparator;
    }

    /**
     * @param string $thousands_separator
     *
     * @return Currency
     */
    public function setThousandsSeparator($thousands_separator)
    {
        $this->thousandsSeparator = $thousands_separator;

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
    public function load($iso)
    {
        $this->setIso($iso);
        $this->loadCurrency();

        return $this;
    }

    public static function make($iso)
    {
        return new self($iso);
    }

    private function loadCurrency()
    {
        $iso        = $this->getIso();

        $currency   = $this->getOneFromConfig($iso);

        $this->setNumericIso($currency['iso_numeric'])
             ->setName($currency['name'])
             ->setSymbol($currency['symbol'])
             ->setAltSymbols($currency['alternate_symbols'])
             ->setSubunit($currency['subunit'])
             ->setSubunitToUnit($currency['subunit_to_unit'])
             ->toggleSymbolPosition($currency['symbol_first'])
             ->setHtmlEntity($currency['html_entity'])
             ->setDecimalMark($currency['decimal_mark'])
             ->setThousandsSeparator($currency['thousands_separator']);
    }

    /**
     * @param string $iso
     * @param array  $currency
     *
     * @return Currency
     */
    public static function loadFromArray($iso, array $currency)
    {
        return (new self)
            ->setIso($iso)
            ->setNumericIso($currency['iso_numeric'])
            ->setName($currency['name'])
            ->setSymbol($currency['symbol'])
            ->setAltSymbols($currency['alternate_symbols'])
            ->setSubunit($currency['subunit'])
            ->setSubunitToUnit($currency['subunit_to_unit'])
            ->toggleSymbolPosition($currency['symbol_first'])
            ->setHtmlEntity($currency['html_entity'])
            ->setDecimalMark($currency['decimal_mark'])
            ->setThousandsSeparator($currency['thousands_separator']);
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
        if ( ! is_numeric($amount) ) {
            throw new InvalidTypeException('The amount must be a numeric value.');
        }

        $amount = round($amount, 2, PHP_ROUND_HALF_UP);
        $amount = number_format($amount, 2, $this->getDecimalMark(), $this->getThousandsSeparator());

        if ( ! $addSymbol ) {
            return $amount;
        }

        return $this->isSymbolFirst()
            ? $this->getSymbol() . ' ' . $amount
            : $amount . ' ' . $this->getSymbol();
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
    private function checkIso(&$iso)
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
     * @param string $iso
     *
     * @return bool
     */
    private function isExists($iso)
    {
        return ! is_null($this->getOneFromConfig($iso));
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    private function getOneFromConfig($key)
    {
        $key = strtolower($key);

        return array_get(get_currencies(), 'iso.' . $key, null);
    }
}
