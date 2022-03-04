<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (Exception $e) {
            if ($e instanceof NotFoundHttpException) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Not found'
                ], 404);
            } else if($e instanceof ValidationException) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Data incorrect',
                    'errors' => $e->errors()
                ], 422);
            } else if($e instanceof AuthenticationException) {
                return response()->json([
                    'status' => 401,
                    'message' => 'You are not authenticated',
                ], 401);
            }
        });
    }
}
