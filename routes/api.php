<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/password/forgot', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('/password-reset', [ResetPasswordController::class, 'reset']);
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/email/resend', [VerificationController::class, 'resend']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
});