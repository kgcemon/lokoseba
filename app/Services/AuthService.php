<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * @throws ValidationException
     */
    public function login(array $credentials)
    {
        $user = User::where('phone', $credentials['phone'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
          return response()->json([
              'error' => true,
              'msg' => 'Invalid credentials'
          ]);
        }

        if (!$user->verified || !$user->profile_complete) {
            return response()->json([
                'error' => true,
                'msg' => 'Your account is not verified'
            ],322);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'error'   => false,
            'message' => 'Login successful',
            'token'   => $token,
            'user'    => $user,
        ];
    }

    public function userRegister($request)
    {
        // Validation
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string|unique:users,phone',
            'image'    => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'password' => 'required|string|min:4',
            'division' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'area'     => 'required|string|max:255',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('users', 'public');
        }else{
            return response()->json([
                'error' => true,
                'msg' => 'Image not uploaded'
            ]);
        }

        // Create user
        $user = User::create([
            'name'     => $validated['name'],
            'phone'    => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'division' => $validated['division'],
            'district' => $validated['district'],
            'area'     => $validated['area'],
            'image'    => $imagePath,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'error'   => false,
            'message' => 'Registration successful',
            'token'   => $token,
            'user'    => $user,
        ]);
    }


public function providerRegister($request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string|unique:users,phone',
            'selfie'    => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'password' => 'required|string|min:4',
            'division' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'area'     => 'required|string|max:255',
            'nid_front' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'nid_back' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $selfiePath   = isset($credentials['selfie'])   ? $credentials['selfie']->store('providers/selfies', 'public') : null;
        $nidFrontPath = isset($credentials['nid_front']) ? $credentials['nid_front']->store('providers/nid', 'public') : null;
        $nidBackPath  = isset($credentials['nid_back'])  ? $credentials['nid_back']->store('providers/nid', 'public') : null;

        // Create user
        $user = User::create([
            'name'       => $credentials['name'],
            'phone'      => $credentials['phone'],
            'password'   => Hash::make($credentials['password']),
            'division'   => $credentials['division'],
            'district'   => $credentials['district'],
            'area'       => $credentials['area'],
            'selfie'     => $selfiePath,
            'nid_front'  => $nidFrontPath,
            'nid_back'   => $nidBackPath,
        ]);

        // Generate token
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'error'   => false,
            'message' => 'Provider registration successful',
            'token'   => $token,
            'user'    => $user,
        ];
    }



}
