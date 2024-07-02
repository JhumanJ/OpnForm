<?php

use App\Http\Controllers\Passport\ApproveAuthorizationController;
use App\Http\Controllers\Passport\AuthorizationController;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController;

Route::post('oauth/token', [AccessTokenController::class, 'issueToken']);

Route::middleware('auth:api')
    ->name('passport.authorizations.')
    ->group(function () {
        Route::get('oauth/authorize', [AuthorizationController::class, 'authorize'])->name('authorize');
        Route::post('oauth/authorize', [ApproveAuthorizationController::class, 'approve'])->name('approve');
    });
