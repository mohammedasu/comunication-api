<?php

namespace App\Registry;

use App\Services\Contracts\EmailTransport;
use Exception;

class EmailRegistry
{
    protected $transport = [];

    /**
     * Register Email transport in array
     * @since 1.0.0
     * @param $name
     * @param EmailTransport $instance
     */
    function register($name, EmailTransport $instance) {
        $this->transport[$name] = $instance;
    }

    /**
     * Get Email Transport class by key
     * @since 1.0.0
     * @param string $name
     * @return mixed
     * @throws Exception
     */
    function get($name) {
        if (array_key_exists($name, $this->transport)) {
            return $this->transport[$name];
        } else {
            throw new Exception("Invalid email transport selection");
        }
    }

}
