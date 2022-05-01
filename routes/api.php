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
    ->group(function () {
        Route::put('/', 'changeProfile');
        Route::put('/bio', 'changeBio')->middleware(['is.teacher']);
        Route::post('/photo', 'changePhotoProfile');
        Route::put('/password', 'changePassword');
    });


Route::prefix('public')
    ->group(function () {
        Route::get('/bank-account', [\App\Http\Controllers\Rest\ApplicationBankAccountController::class, 'getAllBankAccount']);
        Route::get('/level', [\App\Http\Controllers\Rest\LevelController::class, 'getAllLevel']);
        Route::get('/lesson-subject', [\App\Http\Controllers\Rest\LessonSubjectController::class, 'getAllLessonSubject']);
        Route::get('/teacher', [\App\Http\Controllers\Rest\TeacherController::class, 'getTeacher']);
        Route::get('/teacher/recommendation', [\App\Http\Controllers\Rest\TeacherController::class, 'getRecomendedTeacher']);
        Route::get('/teacher/{idTeacher}', [\App\Http\Controllers\Rest\TeacherController::class, 'getDetailTeacher']);
        Route::get('/level', [\App\Http\Controllers\Rest\LevelController::class, 'getAllLevel']);
    });


Route::middleware(['auth:sanctum', 'is.admin'])
    ->prefix('admin')
    ->group(function () {
        Route::controller(\App\Http\Controllers\Rest\DashboardController::class)
            ->prefix('dashboard')
            ->group(function () {
                Route::get('/', 'getTotalUserAndTransaction');
            });

        Route::controller(\App\Http\Controllers\Rest\UserController::class)
            ->prefix('user')
            ->group(function () {
                Route::get('/student', 'getListStudent');
                Route::get('/teacher', 'getListTeacher');
                Route::put('/teacher/{teacherId}/recommendation-date', 'updateRecommendationTeacherDate');
            });

        Route::controller(\App\Http\Controllers\Rest\TransactionController::class)
            ->prefix('transaction')
            ->group(function () {
                Route::get('/', 'getAllTransaction');
                Route::get('/{transactionId}', 'getOneTransaction');
                Route::put('/{transactionId}', 'updateTransactionStatus');
            });

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

        Route::controller(\App\Http\Controllers\Rest\WalletController::class)
            ->prefix('request-wallet')
            ->group(function () {
                Route::get('/', 'getListTeacherRequestWallet');
                Route::post('/{requestWalletId}', 'updateTeacherRequestWalletEvidance');
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

        Route::controller(\App\Http\Controllers\Rest\WalletController::class)
            ->prefix('wallet')
            ->group(function () {
                Route::get('/', 'getMyWallet');
                Route::get('/request-wallet', 'getListTeacherRequestWallet');
                Route::get('/request-wallet/{requestWallerId}', 'getOneTeacherRequestWallet');
                Route::post('/request-wallet', 'createRequestWallet');
            });
    });

Route::middleware(['auth:sanctum', 'is.student'])
    ->prefix('student')
    ->group(function () {
        Route::prefix('teacher')
            ->group(function () {
                Route::get('/history', [\App\Http\Controllers\Rest\TeacherController::class, 'getLastHireTeacher']);
                Route::post('/{teacherId}/hire', [\App\Http\Controllers\Rest\TeacherController::class, 'hireTeacher']);
                Route::post('/{teacherId}/feedback', [\App\Http\Controllers\Rest\TeacherFeedbackController::class, 'giveTeacherFeedback']);
            });

        Route::controller(\App\Http\Controllers\Rest\TransactionController::class)
            ->prefix('transaction')
            ->group(function () {
                Route::get('/', 'getAllTransaction');
                Route::get('/{transactionId}', 'getOneTransaction');
                Route::put('/{transactionId}/changeTransaferMethod', 'changeTransferMethodStudent');
                Route::post('/{transactionId}/uploadTranferEvidance', 'uploadTransferEvidanceStudent');
            });


        Route::controller(\App\Http\Controllers\Rest\ScheduleController::class)
            ->prefix('schedule')
            ->group(function () {
                Route::get('/', 'getStudentSchedule');
                Route::get('/{scheduleId}/detail', 'getStudentScheduleDetail');
                Route::put('/{scheduleId}/detail/{scheduleDetailId}', 'updateStudentScheduleDetail');
            });
    });
