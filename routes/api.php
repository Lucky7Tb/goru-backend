<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(\App\Http\Controllers\Rest\AuthController::class)
    ->prefix("/auth")
    ->group(function() {
        Route::post("login", "login")->middleware(["guest"]);
        Route::post("register", "register")->middleware(["guest"]);
        Route::post("logout", "logout")->middleware("auth:sanctum");
    });

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
