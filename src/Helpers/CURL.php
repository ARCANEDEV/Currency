<?php namespace Arcanedev\Currency\Helpers;

class CURL
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    protected $ch;

    protected $result;

    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
     */
    function __construct()
    {
        $this->ch = curl_init();
    }

    /* ------------------------------------------------------------------------------------------------
     |  Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @param string $url
     *
     * @return mixed
     */
    public function sendRequest($url)
    {
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_HEADER, false);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_FAILONERROR, true);

        $this->result = curl_exec($this->ch);
        curl_close($this->ch);

        return $this->result;
    }
}
