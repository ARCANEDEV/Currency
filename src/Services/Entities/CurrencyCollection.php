<?php namespace Arcanedev\Currency\Services\Entities;

use Arcanedev\Currency\Support\Collection;

class CurrencyCollection extends Collection
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
    public function __construct($items = [])
    {
        if ( ! is_null($items) and ! empty($items) ) {
            parent::__construct($items);
        }
        else {
            $this->loadCurrencies();
        }
    }

    protected function loadCurrencies()
    {
        $currencies = array_get(get_currencies(), 'iso');

        foreach ($currencies as $iso => $currency) {
            $iso        = $this->prepareIso($iso);
            $currency   = Currency::loadFromArray($iso, $currency);

            $this->put($iso, $currency);
        }
    }

    /* ------------------------------------------------------------------------------------------------
     |  Check Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @param string $iso
     *
     * @return bool
     */
    public function has($iso)
    {
        $iso   = $this->prepareIso($iso);

        return parent::has($iso);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @param string $iso
     *
     * @return string
     */
    protected function prepareIso($iso)
    {
        $this->checkIso($iso);

        return strtoupper(trim($iso));
    }

    /**
     * @param string $iso
     */
    protected function checkIso($iso)
    {
        // TODO: Implements the checkIso($iso) function
    }
}
