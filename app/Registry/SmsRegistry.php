<?php

namespace App\Registry;

use App\Services\Contracts\SmsTransport;
use Exception;

class SmsRegistry
{
    protected $transport = [];

    /**
     * Register Sms transport in array
     * @since 1.0.0
     * @param $name
     * @param SmsTransport $instance
     */
    function register($name, SmsTransport $instance) {
        $this->transport[$name] = $instance;
    }

    /**
     * Get Sms Transport class by key
     * @since 1.0.0
     * @param string $name
     * @return mixed
     * @throws Exception
     */
    function get($name) {
        if (array_key_exists($name, $this->transport)) {
            return $this->transport[$name];
        } else {
            throw new Exception("Invalid sms transport selection");
        }
    }

}
