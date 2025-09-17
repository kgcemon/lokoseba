<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Services\AuthService;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    protected $authService;
    public $otpService;

    public function __construct(AuthService $authService, OtpService $otpService)
    {
        $this->authService = $authService;
        $this->otpService = $otpService;
    }


    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'role' => 'required|string|in:user,provider',
        ]);

        if ($request->input('role') == 'user') {
            $result = $this->authService->userRegister($request);
            return response()->json($result, 201);
        }else if ($request->input(['role']) == 'provider') {
            $result = $this->authService->providerRegister($request);
            return response()->json($result, 201);
        }

        return response()->json([
            'error'=> true,
            'message'=> 'Role not allowed'
        ], 401);
    }


    public function login(Request $request)
    {
        $validated = $request->validate([
            'phone'    => 'required|string|max:12|exists:users,phone',
            'password' => 'required|string|min:4',
        ]);

        return $this->authService->login($validated);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        if (!$user->verified) {
            return response()->json([
                'error'=> true,
                'message'=> 'User not verified'
            ]);
        }
        return response()->json([
            'error'=> false,
            'message'=> 'user',
            'data'=> [
                'user' => $user
            ]
        ]);
    }

    public function sendOtp(Request $request)
    {
        return $this->otpService->sendOtp($request);
    }

    public function verifyOtp(Request $request){
        $validated = $request->validate([
            'otp' => 'required|string|min:4',
        ]);

        $user = $request->user();

        $checkOtp = Otp::where('otp', $validated['otp'])->where('user_id', $user->id)->first();
        if ($checkOtp) {
            $user->verified = true;
            $user->save();
            return response()->json([
                'error'=> false,
                'message'=> 'OTP verified, thank you your account now verified'
            ]);
        }
        return response()->json([
            'error'=> true,
            'message'=> 'OTP not verified'
        ],422);
    }



}
