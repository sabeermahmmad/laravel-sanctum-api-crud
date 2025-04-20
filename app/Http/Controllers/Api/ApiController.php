<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PasswordResetTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class ApiController extends Controller
{
    // Registration Api
    // POST [name, email, password]
   
    public function register(Request $request)
{
    // Validation
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'user_role' => 'nullable|integer|in:1,2', // Optional, must be 1 or 2
    ]);

    // User Creation
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'user_role' => $request->user_role ?? 2, // Default to 2 (user)
        'password' => Hash::make($request->password),
    ]);

    return response()->json([
        'message' => 'User registered successfully',
        'status' => true,
        'data' => [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'user_role' => $user->user_role,
            ],
        ],
    ], 201);
}


    // Login Api
    // POST [email, password]
    public function login(Request $request)
    {
        // Validation
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
    
        // Email Check
        $user = User::where('email', $request->email)->first();
    
        // Password Check
        if (!empty($user)) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('myToken')->plainTextToken;
                return response()->json([
                    'message' => 'User logged in successfully',
                    'status' => true,
                    'token' => $token,
                    'data' => [
                        'user' => [
                            'name' => $user->name,
                            'email' => $user->email,
                            'user_role' => $user->user_role,
                        ],
                    ],
                ]);
            } else {
                return response()->json([
                    'message' => 'Invalid password',
                    'status' => false,
                    'data' => [],
                ], 401);
            }
        } else {
            return response()->json([
                'message' => "Email doesn't match with records",
                'status' => false,
                'data' => [],
            ], 404);
        }
    }
  
    // Profile Api
    // GET [Auth: Token]
 

public function profile()
{
    try {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated access. Please provide a valid token.',
                'data' => [],
            ], 401);
        }

        // Extra check: essential fields must exist
        if (empty($user->name) || empty($user->email)) {
            return response()->json([
                'status' => false,
                'message' => 'User information is incomplete. Please contact support.',
                'data' => [],
            ], 422);
        }

        // Log access
        \Log::info('Profile accessed', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_role' => $user->user_role,
        ]);

        // Base user data
        $data = [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'user_role' => $user->user_role,
            ],
        ];

        // Role-based logic
        if ((int) $user->user_role === 1) {
            $data['dashboard_type'] = 'admin';
            $data['dashboard_url'] = '/a/dashboard';

            $data['admin_stats'] = [
                'total_users' => User::count(),
                'system_status' => 'OK',
            ];
        } else {
            $data['dashboard_type'] = 'user';
            $data['dashboard_url'] = '/u/dashboard';
            $data['user_details'] = [
                'created_at' => $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A',
            ];
        }

        return response()->json([
            'status' => true,
            'message' => 'Profile retrieved successfully.',
            'data' => $data,
        ]);

    } catch (\Exception $e) {
        \Log::error('Profile retrieval failed: ' . $e->getMessage());

        return response()->json([
            'status' => false,
            'message' => 'An unexpected error occurred while fetching the profile.',
            'error' => $e->getMessage(),
            'data' => [],
        ], 500);
    }
}


    // Logout Api
    // GET [Auth: Token]

    public function logout(){
        auth()->user()->tokens()->delete();

        return response()->json([
            "status" => true,
            "message" => "Your Logged Out",
            "data" => []
        ]);
    }


// Change Password API
// POST [Auth: Token] with fields: current_password, new_password, new_password_confirmation


public function changePassword(Request $request)
{
    try {
        // Validation
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        // Check if user is authenticated
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated access or Invalid Token. Please login first.',
                'data' => [],
            ], 401);
        }

        // Prevent using the same password
        if ($request->current_password === $request->new_password) {
            return response()->json([
                'status' => false,
                'message' => 'New password cannot be the same as the current password.',
                'data' => [],
            ], 422);
        }

        // Check if current password is valid
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Current password is incorrect. Please try again.',
                'data' => [],
            ], 400);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Password changed successfully.',
            'data' => [],
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'status' => false,
            'message' => 'Validation failed.',
            'errors' => $e->errors(),
            'data' => [],
        ], 422);
    } catch (\Exception $e) {
        \Log::error('Change password failed: ' . $e->getMessage());

        return response()->json([
            'status' => false,
            'message' => 'An unexpected error occurred while changing the password.',
            'error' => $e->getMessage(),
            'data' => [],
        ], 500);
    }
}


public function forgotPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
    ]);

    // Delete previous reset requests
    PasswordResetTokens::where('email', $request->email)->delete();

    $token = Str::random(64);

    PasswordResetTokens::create([
        'email' => $request->email,
        'token' => $token,
        'created_at' => Carbon::now(),
    ]);

    // Return token (or send via mail)
    return response()->json([
        'status' => true,
        'message' => 'Password reset token generated.',
        'token' => $token, // Remove this in production
        'data' => [],
    ]);
}


public function resetPassword(Request $request)
{
    // Step 1: Validate input
    $request->validate([
        'email' => 'required|email|exists:users,email',
        'token' => 'required|string',
        'new_password' => 'required|string|min:8|confirmed',
    ]);

    // Step 2: Check reset token from password_reset_tokens table
    $reset = PasswordResetTokens::where('email', $request->email)
                ->where('token', $request->token)
                ->first();

    if (!$reset) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid or expired reset token.',
            'data' => [],
        ], 400);
    }

    // Optional: Check token age (e.g., 60 mins)
    if (Carbon::parse($reset->created_at)->diffInMinutes(now()) > 1) {
        return response()->json([
            'status' => false,
            'message' => 'Reset token has expired.',
            'data' => [],
        ], 400);
    }

    // Step 3: Update user password
    $user = User::where('email', $request->email)->first();
    $user->password = Hash::make($request->new_password);
    $user->save();

    // Step 4: Delete used token
    $reset->delete();

    // Step 5: Return success response
    return response()->json([
        'status' => true,
        'message' => 'Password has been reset successfully.',
        'data' => [],
    ]);
}


}
