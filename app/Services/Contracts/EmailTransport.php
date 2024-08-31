<?php

namespace App\Services\Contracts;

interface EmailTransport
{
    public function request(array $params);
}
