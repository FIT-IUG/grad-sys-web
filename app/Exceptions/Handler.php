<?php

namespace App\Exceptions;

use ErrorException;
use Exception;
use Google\Cloud\Core\Exception\ServiceException;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

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
     * @param Exception $exception
     * @return void
     *
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        if ($exception instanceof ConnectException) {
        }
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Exception $exception
     * @return Response
     *
     * @throws Exception
     */
    public function render($request, Exception $exception)
    {
        if (env('APP_ENV') === 'production')
            if ($exception instanceof ConnectException) {
                return redirect()->route('logout')->with('error', 'حدثت مشكلة في الاتصال الرجاء المحاولة مرة أخرى.');
            } elseif ($exception instanceof ServiceException) {
                return redirect()->route('logout')->with('error', 'حدثت مشكلة في الاتصال الرجاء المحاولة مرة أخرى.');
            } elseif ($exception instanceof ErrorException) {
                return redirect()->route('login')->with('error', 'حاول أحدهم الدخول إلى حسابك.');
            }elseif ($exception instanceof InvalidArgumentException) {
                return redirect()->route('login')->with('error', 'حصلت مشكلة بالنظام.');
            }

        return parent::render($request, $exception);
    }
}
