<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CheckLogController;
use App\Http\Controllers\Api\CreditController;
use App\Http\Controllers\Api\MapBoxController;
use App\Http\Controllers\Api\OvertimeController;
use App\Http\Controllers\Api\ReceiptController;
use Illuminate\Support\Facades\Route;

Route::middleware('xss')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/geoaddress', [MapBoxController::class, 'getAddress']);

    Route::middleware(['api', 'auth:api', 'singledevice'])->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/user', [AuthController::class, 'updateProfile']);
        Route::post('/update-photo', [AuthController::class, 'updatePhotoProfile']);
        Route::post('/password', [AuthController::class, 'password']);
        Route::post('/logout', [AuthController::class, 'logout']);

        // Checklog
        Route::prefix('checklog')->group(function () {
            Route::post('/in', [CheckLogController::class, 'in']);
            Route::get('/check-when-exists', [CheckLogController::class, 'checkWhenExists']);
            Route::post('/{checklog}/out', [CheckLogController::class, 'out']);
            Route::get('/list', [CheckLogController::class, 'list']);
            Route::get('/{checklog}/show', [CheckLogController::class, 'show']);
        });

        // Overtime
        Route::prefix('overtime')->group(function () {
            Route::get('/', [OvertimeController::class, 'index']);
            Route::get('/{overtime}/show', [OvertimeController::class, 'show']);
            Route::post('/{overtime}/login', [OvertimeController::class, 'login']);
            Route::post('/{overtime}/{overtimelog}/logdone', [OvertimeController::class, 'logdone']);
        });

        // Attendance
        Route::prefix('attendance')->group(function () {
            Route::get('/', [AttendanceController::class, 'index']);
            Route::post('/store', [AttendanceController::class, 'store']);
            Route::get('/{attendance}/show', [AttendanceController::class, 'show']);
        });

        // Receipt / Kas bon
        Route::prefix('receipt')->group(function () {
            Route::get('/limit', [ReceiptController::class, 'limit']);
            Route::post('/store', [ReceiptController::class, 'store']);
            Route::get('/list', [ReceiptController::class, 'list']);
        });

        Route::prefix('credit')->group(function () {
            Route::get('/list', [CreditController::class, 'list']);
            Route::get('/list/detail/{uuid}', [CreditController::class, 'detail']);
            Route::post('/store', [CreditController::class, 'store']);
            Route::get('/total', [CreditController::class, 'total']);
        });
    });
});
