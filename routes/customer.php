<?php

use App\Http\Controllers\Customer\AuthController;
use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\Customer\LicenseController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::prefix('customer')->group(function () {
    Route::get('/', [AuthController::class, 'index'])->name('member.login');
    Route::post('/login', [AuthController::class, 'login'])->name('member.login.post')->middleware('throttle:5,1');

    Route::get('/register', [AuthController::class, 'register'])->name('member.register');
    Route::post('/register', [AuthController::class, 'registerProcess'])->name('member.register.post');
    Route::get('/register/{uuid}/verification', [AuthController::class, 'verification'])->name('member.register.verification');
    Route::post('/register/{uuid}/verification', [AuthController::class, 'verificationProcess'])->name('member.register.verification.process');
    Route::get('/register/{uuid}/resend', [AuthController::class, 'resendVerfication'])->name('member.register.verification.resend')->middleware('throttle:5,1');
    Route::get('/register/{uuid}/data', [AuthController::class, 'data'])->name('member.register.data');
    Route::post('/register/{uuid}/data', [AuthController::class, 'validationData'])->name('member.register.validationdata');

    Route::middleware('member.auth')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('member.dashboard');
        Route::get('/logout', [DashboardController::class, 'logout'])->name('member.logout');

        Route::prefix('license')->group(function () {
            Route::get('/', [LicenseController::class, 'index'])->name('member.license.index');
            Route::get('/show/{license}', [LicenseController::class, 'show'])->name('member.license.show');
            Route::get('/create', [LicenseController::class, 'create'])->name('member.license.create');
            Route::post('/store', [LicenseController::class, 'store'])->name('member.license.store');
        });

        Route::prefix('payment')->group(function () {
            Route::get('/channel-list', [PaymentController::class, 'listChannel'])->name('payment.channel.list');
        });

        Route::prefix('profile')->group(function () {
            Route::get('/', [ProfileController::class, 'index'])->name('member.profile');
            Route::post('/', [ProfileController::class, 'profileProcess'])->name('member.profile.process');

            Route::get('/password', [ProfileController::class, 'password'])->name('member.profile.password');
            Route::post('/password', [ProfileController::class, 'passwordProcess'])->name('member.profile.password.post');
        });
    });
});
