<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
//        $this->reportable(function (Throwable $e) {
//            //
//        });

        $this->renderable(function (NotFoundHttpException $e,$request){
            if($request->is('api/*')){
                return makeResponse('error','Route Not Found',404);
            }
        });
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
//            $json = [
//                'status'=>401,
//                'message' => $exception->getMessage()
//            ];
            return makeResponse('error',$exception->getMessage(),401);
        }
    }

    public function report(Throwable $exception)
    {
        parent::report($exception);
//        return makeResponse('error',$exception->getMessage(),404);

    }

}
