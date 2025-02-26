<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use AppLog;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        $response = [];
        $response['request_id'] = AppLog::getRequestId();
        $response['success'] = false;
        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {

            $response['response_code'] = 104;
            $response['message'] = 'Record not found';
            return response()->json($response, 404);
        } elseif ($exception instanceof ValidationFailedException) {

            $response['response_code'] = 101;
            $response['message'] = $exception->getMessage();
            return response()->json($response, 400);
        }

        return parent::render($request, $exception);
    }
}
