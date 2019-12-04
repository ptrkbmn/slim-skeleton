<?php

namespace App\Helpers;

class URLHelper
{
    private $requestUrl;
    private $refererUrl;

    public function __construct()
    {
        $host = filter_input(INPUT_SERVER, 'HTTP_HOST');
        $this->requestUrl = filter_input(INPUT_SERVER, 'REQUEST_URI');
        $this->refererUrl = filter_input(INPUT_SERVER, 'HTTP_REFERER');
        if ($this->refererUrl) {
            $hostIndex = stripos($this->refererUrl, $host);
            if ($hostIndex !== false) {
                $this->refererUrl = substr($this->refererUrl, $hostIndex +  strlen($host));
            }
        }
    }

    /**
     * Returns the cancel URL for within a certain
     * form. It either returns the previous or the
     * parent URL of the current URL.
     */
    public function cancel()
    {
        if ($this->refererUrl && $this->refererUrl != $this->requestUrl) {
            return $this->refererUrl;
        }

        // Remove last portion of the url
        $lastIndex = strripos($this->requestUrl, "/");
        if ($lastIndex === false)
            return $this->requestUrl;
        $cancelUrl = substr($this->requestUrl, 0, $lastIndex);

        // If the url ends on delete, remove it
        $deleteIndex = strripos($cancelUrl, "/delete");
        if ($deleteIndex !== false) {
            return substr($cancelUrl, 0, $deleteIndex);
        }

        return $cancelUrl;
    }
}
