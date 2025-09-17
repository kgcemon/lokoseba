<?php

namespace App\Services;

use App\Models\Otp;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;

class OtpService{
    public function sendOtp($request)
    {
        $apiKey = env('SMS_API_KEY');
        $senderId = env('SMS_SENDER_KEY');
        $code = random_int(100000, 999999);
        $message = "আপনার লোকসেভা ভেরিফাই কোডঃ $code";
        $user = $request->user();
        if (!$user->verified) {
            try {
                $response = Http::get("https://bulksmsbd.net/api/smsapi?api_key=$apiKey&type=text&number=$user->phone&senderid=$senderId&message=$message");
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
        }else{
            return response()->json([
                'error'=> false,
                'message'=> 'your account is already verified'
            ]);
        }
    }
}
