<?php

namespace App\Http\Controllers;

use App\Exceptions\ValidationFailedException;
use App\Helpers\AppLogHelper as AppLog;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use ValidatesRequests;

    protected function response($return)
    {
        $return['request_id'] = AppLog::getRequestId();
        $http_code = (isset($return['http_code'])) ? $return['http_code'] : 200;
        return response()->json($return, $http_code);
    }

    /**
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @throws ValidationFailedException
     */
    protected function validate(array $data, array $rules, array $messages = [])
    {
        if (!empty($messages)) {
            $validator = Validator::make($data, $rules, $messages);
        } else {
            $validator = Validator::make($data, $rules);
        }

        if ($validator->fails()) {
            $message = implode(',', $validator->errors()->all());
            throw new ValidationFailedException($message);
        }
    }
}
