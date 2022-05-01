<?php

namespace App\Exceptions;

use Throwable;
use InvalidArgumentException;
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

        $this->renderable(function(ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'message' => 'Data tidak ditemukan'
            ], 404);
        });

        $this->renderable(function (NotFoundHttpException $e) {
            return response()->json([
                'status' => 404,
                'message' => "Tidak ditemukan"
            ], 404);
        });

        $this->renderable(function (ValidationException $e) {
            return response()->json([
                'status' => 422,
                'message' => 'Data salah',
                'errors' => $e->errors()
            ], 422);
        });

        $this->renderable(function (AuthenticationException $e) {
            return response()->json([
                'status' => 401,
                'message' => 'Kamu belum login, harap login terlebih dahulu',
            ], 401);
        });
    }
}
