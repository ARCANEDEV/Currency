<?php namespace Arcanedev\Currency\Services\Entities;

use Arcanedev\Currency\Support\Collection;

class CurrencyCollection extends Collection
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
     protected $items;

    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
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

    private function loadCurrencies()
    {
        $currencies = array_get(get_currencies(), 'iso');

        foreach ($currencies as $iso => $currency) {
            $iso        = strtoupper($iso);
            $currency   = Currency::loadFromArray($iso, $currency);

            $this->put($iso, $currency);
        }
    }

    /* ------------------------------------------------------------------------------------------------
     |  Check Functions
     | ------------------------------------------------------------------------------------------------
     */
     public function has($iso)
     {
         $iso   = strtoupper($iso);

         return parent::has($iso);
     }
}
