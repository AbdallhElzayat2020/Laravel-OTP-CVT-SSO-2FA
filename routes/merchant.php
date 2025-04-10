<?php


use App\Http\Controllers\CustomVerificationTokenController;
use App\Http\Controllers\MerchantAuth\AuthenticatedSessionController;
use App\Http\Controllers\MerchantAuth\ConfirmablePasswordController;
use App\Http\Controllers\MerchantAuth\EmailVerificationNotificationController;
use App\Http\Controllers\MerchantAuth\EmailVerificationPromptController;
use App\Http\Controllers\MerchantAuth\NewPasswordController;
use App\Http\Controllers\MerchantAuth\PasswordController;
use App\Http\Controllers\MerchantAuth\PasswordResetLinkController;
use App\Http\Controllers\MerchantAuth\RegisteredUserController;
use App\Http\Controllers\MerchantAuth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:merchant')->prefix('merchant')->group(function () {

    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('merchant.register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('merchant.login');

    Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('merchant.handle-login');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('merchant.password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('merchant.password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('merchant.password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('merchant.password.store');

});


Route::middleware(['auth:merchant'])->prefix('merchant')->group(function () {

    /* Check for email Verification is Enabled or Not */
    if (config('verification.way') === 'email') {

        Route::get('verify-email', EmailVerificationPromptController::class)
            ->name('merchant.verification.notice');

        Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
            ->middleware(['signed', 'throttle:6,1'])
            ->name('merchant.verification.verify');

        Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('merchant.verification.send');

    }


    if (config('verification.way') === 'cvt') {

        Route::get('verify-email', [CustomVerificationTokenController::class, 'notice'])
            ->name('merchant.verification.notice');

        Route::get('verify-email/{id}/{token}', [CustomVerificationTokenController::class, 'verify'])
            ->middleware(['throttle:6,1'])
            ->name('merchant.verification.verify');

        Route::post('email/verification-notification', [CustomVerificationTokenController::class, 'resend'])
            ->middleware('throttle:6,1')
            ->name('merchant.verification.send');

    }


    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('merchant.password.confirm');

    Route::post('merchant.confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('merchant.password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('merchant.logout');

});
