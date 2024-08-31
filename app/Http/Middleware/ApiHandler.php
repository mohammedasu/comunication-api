<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;
use DB;
use Config;
use Illuminate\Support\Facades\Log;
use AppLog;
use App\Services\Traits\ResponseCodeTrait;

class ApiHandler
{
    use ResponseCodeTrait;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $timeStart = microtime(true);

        /* check for X-Request-ID header */
        $request_id = ( isset($request->header()['x-request-id'][0]) ) ? $request->header()['x-request-id'][0]: NULL;
        if( empty($request_id) ){
            $result = $this->getResponseCode(103);
            $http_code = $result['http_code'];
            unset($result['http_code']);
            return response()->json($result,$http_code);
        }

        AppLog::setRequestId($request_id);

        /* Logging Api params,headers values */
        Log::channel('apilog')->info($request_id.'|request_url', [$request->server()['REQUEST_URI']]);
        if( !empty($request->all()) ){
            Log::channel('apilog')->info($request_id.'|request_parameters', $request->all());
        }
        Log::channel('apilog')->debug($request_id.'|header_parameters', $request->header());

        /* handover to route and controller */
        $response = $next($request);

        Log::channel('apilog')->info($request_id.'|response', (array) json_decode($response->content(),true) );
        
        return $response;
    }

}
