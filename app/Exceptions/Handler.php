<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof HttpException) {
            $statusCode = $e->getStatusCode();
            return response()->json([
                'msg' => Response::$statusTexts[$statusCode]
            ], $statusCode);
        } else {
            if ($_ENV['APP_DEBUG'] === 'false') {
                return response()->json([
                    'msg' => Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR]
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
        return parent::render($request, $e);
    }
}
