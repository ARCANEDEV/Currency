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
    public function __construct()
    {
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
        $this->ch = curl_init();

        $this->setOptions($url);

        $this->result = curl_exec($this->ch);
        curl_close($this->ch);

        return $this->result;
    }

    /**
     * @param string $url
     */
    private function setOptions($url)
    {
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_HEADER, false);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_FAILONERROR, true);
    }
}
