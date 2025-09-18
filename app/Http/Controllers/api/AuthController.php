<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use App\Services\AuthService;
use App\Services\OtpService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{

    protected $authService;
    public $otpService;

    public function __construct(AuthService $authService, OtpService $otpService)
    {
        $this->authService = $authService;
        $this->otpService = $otpService;
    }


    public function checkNumber(Request $request)
    {
        $validated = $request->validate([
            'number' => 'required|numeric'
        ]);
        $user = User::where('number', $validated['number'])->first();
        if (!$user) {
            return response()->json([
                'error' => true,
                'message' => 'Number not found'
            ],423);
        }
        return response()->json([
            'error' => false,
            'message' => 'Number already have a account',
        ]);
    }

    public function consultant()
    {
        $user = User::where('role', 'consultant')->get();
        return response()->json([
            'error' => false,
            'message' => 'data found',
            'data' => $user
        ]);
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
        $checkOtp = Otp::where('otp', $validated['otp'])->where('created_at', '>=', Carbon::now()->subMinutes(5))
            ->where('user_id', $user->id)->first();
        if ($checkOtp) {
            $user->verified = true;
            $user->save();
            $checkOtp->delete();
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

    public function sendCode(Request $request){
        $validated = $request->validate([
            'phone' => 'required|string|max:12|exists:users,phone',
        ]);
        try {
            $apiKey = env('SMS_API_KEY');
            $senderId = env('SMS_SENDER_KEY');
            $code = random_int(100000, 999999);
            $message = "আপনার লোকসেভা ভেরিফাই কোডঃ $code";
            $phone = $validated['phone'];
            $user = User::where('phone', $phone)->first();
            $response = Http::get("https://bulksmsbd.net/api/smsapi?api_key=$apiKey&type=text&number=$phone&senderid=$senderId&message=$message");
            if ($response->status() == 202) {
                Otp::create([
                    'user_id' => $user->id,
                    'code' => $code,
                ]);
            }
            return response()->json([
                'error'=> false,
                'message'=> 'OTP has been sent successfully'
            ]);
        }catch (\Exception $exception){
            return response()->json([
                'error'=> true,
                'message'=> $exception->getMessage()
            ]);
        }
    }

    public function updatePassword()
    {
        $validated = request()->validate([
            'password' => 'required|string|min:5',
            'otp' => 'required|string|min:6',
            'phone' => 'required|string|max:12|exists:users,phone',
        ]);

        $user = User::where('phone', $validated['phone'])->first();

        $otp = Otp::where('otp', $validated['otp'])
            ->where('created_at', '>=', Carbon::now()->subMinutes(5))
            ->where('user_id', $user->id)->first();
        if (!$otp){
            return response()->json([
                'error'=> true,
                'message'=> 'OTP not valid'
            ]);
        }else{
            $user->password = bcrypt($validated['password']);
            $user->save();
            $otp->delete();
            return response()->json([
                'error'=> false,
                'message'=> 'Password has been updated'
            ]);
        }

    }


}
