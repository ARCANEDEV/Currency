<?php namespace Arcanedev\Currency\Services\Providers;

class GoogleConverter extends BaseProvider
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
    public function convert($from, $to)
    {
        $ratio = $this->getRatio($from, $to);

        return [
            'from'  => $from,
            'to'    => $to,
            'ratio' => $ratio,
        ];
    }
}
