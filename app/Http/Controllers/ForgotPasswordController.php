<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\ResetPassword;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Custom reset URL
       ResetPassword::createUrlUsing(function ($user, string $token) {
    return env('FRONTEND_URL') . '#/reset-password/' . $token . '/' . urlencode($user->email);
});

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Reset link sent to your email.'])
            : response()->json(['message' => 'Unable to send reset link.'], 400);
    }
}
