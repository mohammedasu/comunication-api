<?php

namespace App\Services\Contracts;

interface SmsTransport
{
    public function request(array $params);
}
