<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;
use DB;
use Config;
use Illuminate\Support\Facades\Log;
use AppLog;
use App\Services\Traits\ResponseCodeTrait;

class ApiBasicAuthentication
{
    use ResponseCodeTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (isset($_SERVER["HTTP_AUTHORIZATION"]) && 0 === stripos($_SERVER["HTTP_AUTHORIZATION"], 'basic ')) {
            $exploded = explode(':', base64_decode(substr($_SERVER["HTTP_AUTHORIZATION"], 6)), 2);
            if (2 == \count($exploded)) {
                if($exploded[0] == 'MedisageCommAPI' && $exploded[1] == 'CommAPI@Medi2021') {
                    return $next($request);
                } else {
                    $result = $this->getResponseCode(202);
                }   
            } else {
                $result = $this->getResponseCode(202);
            }
        } else {
            $result = $this->getResponseCode(202);
        }
        $http_code = $result['http_code'];
        
        return response()->json($result,$http_code);
    }
}
