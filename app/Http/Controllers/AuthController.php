<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Utils\ResponseUtil;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        //
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            $user = User::where('email', $credentials['email'])->first();

            if(!$user || !Hash::check($credentials['password'],$user->password)){
                return response()->json([
                    'message' => 'Invalid Credentials'
                ],401);
            }
            $token = $user->createToken($user->name)->plainTextToken;
        } catch (Exception $e) {
            return ResponseUtil::errorResponse($e->getMessage(), 500);
        }

        return ResponseUtil::noticeResponse('success', 200, [
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return ResponseUtil::noticeResponse('Logged out successfully', 200);
    }
}