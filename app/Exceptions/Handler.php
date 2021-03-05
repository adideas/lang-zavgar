<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        //
    ];

    protected $dontFlash  = [
        'password',
        'password_confirmation',
    ];

    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    public function render($request, Throwable $exception)
    {
        if (get_class($exception) == AuthorizationException::class
            && $exception->getMessage() == 'This action is unauthorized.') {
            return response()->json(
                [
                    'errors' => [
                        'message' => [$exception->getMessage()],
                    ],
                ],
                403
            );
        }

        return parent::render($request, $exception);
    }
}
