<?php namespace Arcanedev\Currency\Services\Providers;

use Arcanedev\Currency\Contracts\CurrencyProvider;

class GoogleConverter extends BaseProvider implements CurrencyProvider
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    protected $baseUrl = 'https://www.google.com/finance/converter';

    /**
     * @param string $from
     * @param string $to
     *
     * @return string
     */
    private function prepareQuery($from, $to)
    {
        return '?'. implode('&', [
            "a=1",
            "from=$from",
            "to=$to",
        ]);
    }

    /**
     * @param $from
     * @param $to
     *
     * @return float
     */
    private function getRatio($from, $to)
    {
        $html   = file_get_contents($this->baseUrl . $this->prepareQuery($from, $to));

        preg_match("/<span class=bld>(.*)<\/span>/", $html, $converted);
        $ratio  = (float) preg_replace("/[^0-9.]/", "", $converted[1]);

        return $ratio;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    public function convert($from, $to)
    {
        $ratio = $this->getRatio($from, $to);

        return [
            'from'  => $from,
            'to'    => $to,
            'ratio' => $ratio,
        ];
    }

    /**
     * @param array $currencies
     *
     * @return array
     */
    public function convertMany(array $currencies = [])
    {
        $ratios = [];

        foreach ($currencies as $fromCurrency => $toCurrencies) {
            foreach($toCurrencies as $toCurrency) {
                $ratios[] = $this->convert($fromCurrency, $toCurrency);
            }
        }

        return $ratios;
    }
}
