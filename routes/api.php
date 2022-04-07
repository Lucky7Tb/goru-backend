<?php

use Illuminate\Support\Facades\Route;

Route::controller(\App\Http\Controllers\Rest\AuthController::class)
    ->prefix("/auth")
    ->group(function () {
        Route::post("login", "login")->middleware(["guest"]);
        Route::post("register", "register")->middleware(["guest"]);
        Route::post("logout", "logout")->middleware(["auth:sanctum"]);
    });

Route::controller(\App\Http\Controllers\Rest\ProfileController::class)
    ->prefix('/profile')
    ->middleware(['auth:sanctum'])
    ->group(function() {
        Route::put('/', 'changeProfile');
        Route::put('/bio', 'changeBio')->middleware(['is.teacher']);
        Route::post('/photo', 'changePhotoProfile');
        Route::put('/password', 'changePassword');
    });


Route::prefix('public')
    ->group(function () {
        Route::get('/level', [\App\Http\Controllers\Rest\LevelController::class, 'getAllLevel']);
        Route::get('/lesson-subject', [\App\Http\Controllers\Rest\LessonSubjectController::class, 'getAllLessonSubject']);
    });


Route::middleware(['auth:sanctum', 'is.admin'])
    ->prefix('admin')
    ->group(function () {
        Route::controller(\App\Http\Controllers\Rest\ApplicationBankAccountController::class)
            ->prefix('bank-account')
            ->group(function () {
                Route::get('/', 'getAllBankAccount');
                Route::get('/{bankAccountId}', 'getOneBankAccount');
                Route::post('/', 'createBankAccount');
                Route::put('/{bankAccountId}', 'updateBankAccount');
                Route::post('/{bankAccountId}/logo', 'updateBankAccountLogo');
            });

        Route::controller(\App\Http\Controllers\Rest\LevelController::class)
            ->prefix('level')
            ->group(function () {
                Route::get('/', 'getAllLevel');
                Route::get('/{levelId}', 'getOneLevel');
                Route::post('/', 'createLevel');
                Route::put('/{levelId}', 'updateLevel');
                Route::delete('/{levelId}', 'deleteLevel');
            });

        Route::controller(\App\Http\Controllers\Rest\LessonSubjectController::class)
            ->prefix('lesson-subject')
            ->group(function () {
                Route::get('/', 'getAllLessonSubject');
                Route::get('/{lessonSubjectId}', 'getOneLessonSubject');
                Route::post('/', 'createLessonSubject');
                Route::post('/{lessonSubjectId}/thumbnail', 'updateLessonSubjectThumbnail');
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

        Route::controller(\App\Http\Controllers\Rest\ScheduleController::class)
            ->prefix('schedule')
            ->group(function () {
                Route::get('/', 'getTeacherSchedule');
                Route::get('/{scheduleId}/detail', 'getTeacherScheduleDetail');
                Route::put('/{scheduleId}/detail/{scheduleDetailId}', 'updateTeacherScheduleDetail');
                Route::put('/{scheduleId}/detail/{scheduleDetailId}/meet-link', 'updateScheduleMeetLink');
                Route::post('/{scheduleId}/detail/{scheduleDetailId}/meet-evidance', 'updateScheduleMeetEvidance');
            });

        Route::controller(\App\Http\Controllers\Rest\TeacherDocumentAdditionalController::class)
            ->prefix('document')
            ->group(function () {
                Route::get('/', 'getDocument');
                Route::post('/', 'addDocument');
                Route::delete('/{documentId}', 'deleteDocument');
            });
    });

Route::middleware(['auth:sanctum', 'is.student'])
    ->prefix('student')
    ->group(function () {
        Route::prefix('teacher')
            ->group(function () {
                Route::post('/{teacherId}/hire', [\App\Http\Controllers\Rest\TeacherController::class, 'hireTeacher']);
            });

<<<<<<< HEAD
            Route::controller(\App\Http\Controllers\Rest\TransactionController::class)
            ->prefix('transaction')
            ->group(function () {
                Route::get('/', 'getAllTransaction');
                Route::put('/{transactionId}', 'updateTransactionStudent');
            });

            Route::controller(\App\Http\Controllers\Rest\ScheduleController::class)
=======
        Route::controller(\App\Http\Controllers\Rest\ScheduleController::class)
>>>>>>> 5a0b5e76d415ecbbc185d8f9b0a0f58a0321be89
            ->prefix('schedule')
            ->group(function () {
                Route::get('/', 'getStudentSchedule');
                Route::get('/{scheduleId}/detail', 'getStudentScheduleDetail');
                Route::put('/{scheduleId}/detail/{scheduleDetailId}', 'updateStudentScheduleDetail');
            });
    });
