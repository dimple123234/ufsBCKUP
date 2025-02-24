<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\SellerApplication;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // First check if there's a seller application
        $application = SellerApplication::where('email', $request->email)->first();

        if ($application) {
            if ($application->status === 'pending') {
                throw ValidationException::withMessages([
                    'email' => ['Your seller account is pending approval. Please wait for admin approval.'],
                ]);
            } elseif ($application->status === 'rejected') {
                throw ValidationException::withMessages([
                    'email' => ['Your seller account has been rejected.'],
                ]);
            }
        }

        // Attempt to log in
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // For sellers, check if they are approved
            if ($user->role === 'seller' && !$user->is_approved) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => ['Your seller account has been disabled.'],
                ]);
            }

            $request->session()->regenerate();

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'user' => $user
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['Invalid email or password'],
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ]);
    }
} 