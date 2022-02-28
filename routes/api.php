<?php

use Illuminate\Support\Facades\Route;

Route::controller(\App\Http\Controllers\Rest\AuthController::class)
    ->prefix("/auth")
    ->group(function() {
        Route::post("login", "login")->middleware(["guest"]);
        Route::post("register", "register")->middleware(["guest"]);
        Route::post("logout", "logout")->middleware("auth:sanctum");
    });


Route::middleware(['auth:sanctum', 'is.admin'])
    ->prefix('admin')
    ->group(function() {
        Route::controller(\App\Http\Controllers\Rest\LevelController::class)
            ->prefix('level')
            ->group(function() {
                Route::get('/', 'getAllLevel');
                Route::get('/{levelId}', 'getOneLevel');
                Route::post('/', 'createLevel');
                Route::put('/{levelId}', 'updateLevel');
                Route::delete('/{levelId}', 'deleteLevel');
            });

        Route::controller(\App\Http\Controllers\Rest\LessonSubjectController::class)
            ->prefix('lesson-subject')
            ->group(function() {
                Route::get('/', 'getAllLessonSubject');
                Route::get('/{lessonSubjectId}', 'getOneLessonSubject');
                Route::post('/', 'createLessonSubject');
                Route::put('/{lessonSubjectId}', 'updateLessonSubject');
                Route::delete('/{lessonSubjectId}', 'deleteLessonSubject');
            });

    });
