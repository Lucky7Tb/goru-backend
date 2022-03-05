<?php

use Illuminate\Support\Facades\Route;

Route::controller(\App\Http\Controllers\Rest\AuthController::class)
    ->prefix("/auth")
    ->group(function() {
        Route::post("login", "login")->middleware(["guest"]);
        Route::post("register", "register")->middleware(["guest"]);
        Route::post("logout", "logout")->middleware("auth:sanctum");
    });


Route::prefix('public')
    ->middleware(['auth:sanctum'])
    ->group(function() {
        Route::get('/level', [\App\Http\Controllers\Rest\LevelController::class, 'getAllLevel']);
        Route::get('/lesson-subject', [\App\Http\Controllers\Rest\LessonSubjectController::class, 'getAllLessonSubject']);
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

Route::middleware(['auth:sanctum', 'is.teacher'])
    ->prefix('teacher')
    ->group(function () {
        Route::controller(\App\Http\Controllers\Rest\TeacherLevelController::class)
            ->prefix('level')
            ->group(function () {
                Route::get('/', 'getAllTeacherLevel');
                Route::post('/', 'createTeacherLevel');
                Route::delete('/{teacherLevelId}', 'deleteTeacherLevel');
            });

        Route::controller(\App\Http\Controllers\Rest\TeacherLessonSubjectController::class)
            ->prefix('lesson-subject')
            ->group(function () {
                Route::get('/', 'getAllTeacherLessonSubject');
                Route::post('/', 'createTeacherLessonSubject');
                Route::delete('/{teacherLessonSujectId}', 'deleteTeacherLessonSubject');
            });

        Route::controller(\App\Http\Controllers\Rest\TeacherPackageController::class)
            ->prefix('package')
            ->group(function () {
                Route::get('/', 'getAllTeacherPackage');
                Route::get('/{teacherPackageId}', 'getOneTeacherPackage');
                Route::post('/', 'createTeacherPackage');
                Route::put('/{teacherPackageId}', 'updateTeacherPackage');
                Route::put('/{teacherPackageId}/toggle-status', 'toggleStatusTeacherPackage');
            });
    });
