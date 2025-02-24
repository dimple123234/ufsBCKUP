<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SellerApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SellerApplicationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email|unique:seller_applications,email',
            'password' => 'required|string|min:8',
            'phone' => 'required|string',
            'address' => 'required|string',
        ]);

        try {
            // Create seller application only
            $application = SellerApplication::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'address' => $request->address,
                'status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Your seller application has been submitted successfully. Please wait for admin approval.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Seller application error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit seller application'
            ], 500);
        }
    }
}
