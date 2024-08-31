<?php

namespace App\Exceptions;

use Exception;
use App\Constants\Constants;
use Illuminate\Support\Facades\Log;

class CustomErrorException extends Exception
{
    public function __construct($e, $custom_message = null, $custom_code = null)
    {
        $code = null;
        if (empty($custom_code)) {
            $code = is_int($e->getCode()) ? $e->getCode() : 500;
        }
        $this->code     = $custom_code ? $custom_code : $code;
        $this->message  = $custom_message ? $custom_message : $e->getMessage();
        $this->line  = $e->line ?? null;
    }
    public function render()
    {
        Log::info(['Error handler' => $this->message]);
        Log::info(['Error handler' => $this->code]);

        if (!$this->code) {
            $this->code = Constants::INTERNAL_SERVER_ERROR;
        }
        return response()->json([
            "status"        => false,
            "message"       => $this->message,
            "status_code"   => $this->code,
            "line_no"   => $this->line,
        ], $this->code);
    }
}
