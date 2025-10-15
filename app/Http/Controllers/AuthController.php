<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create($data);

        // ইমেইল ভেরিফিকেশন মেইল পাঠাবে
        event(new Registered($user));

        return response()->json([
            'message' => 'Registration successful. Please check your email for verification link.'
        ], 201);
    }

    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required|string',
    //     ]);

    //     if (!Auth::attempt($request->only('email', 'password'))) {
    //         throw ValidationException::withMessages([
    //             'email' => ['The provided credentials are incorrect.']
    //         ]);
    //     }

    //     $user = Auth::user();

    //     if (!$user->hasVerifiedEmail()) {
    //         return response()->json(['message' => 'Email not verified.'], 403);
    //     }

    //     $token = $user->createToken('api-token')->plainTextToken;

    //     return response()->json([
    //         'access_token' => $token,
    //         'token_type' => 'Bearer',
    //         'user' => $user,
    //     ]);
    // }

    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    $user = \App\Models\User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['message' => 'Invalid email or password.'], 401);
    }

    // 🧠 আগে ভেরিফাই চেক করো
    if (!$user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email not verified.'], 403);
    }

    // ✅ এরপর পাসওয়ার্ড চেক করো
    if (!\Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid email or password.'], 401);
    }

    // 🔑 টোকেন তৈরি করো
    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'access_token' => $token,
        'token_type' => 'Bearer',
        'user' => $user,
    ]);
}


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
