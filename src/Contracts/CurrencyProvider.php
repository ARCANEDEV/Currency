<?php namespace Arcanedev\Currency\Contracts;

interface CurrencyProvider
{
    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    public function convert($from, $to);

    public function convertMany(array $currencies = []);
}
