<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        if (request()->expectsJson()) {
            $profilePictureUrl = $user->profile_picture 
                ? asset('storage/profile_pictures/' . $user->profile_picture)
                : asset('images/admin.png');

            Log::info('Profile picture URL in show method: ' . $profilePictureUrl); // Debug log

            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'profile_picture' => $user->profile_picture,
                    'profile_picture_url' => $profilePictureUrl,
                    'role' => $user->role
                ]
            ]);
        }
        
        return view('app');
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->address = $request->address;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully!',
            'user' => $user
        ]);
    }

    public function uploadProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        try {
            if ($request->hasFile('profile_picture')) {
                Log::info('Profile picture file detected');

                // Make sure the storage directory exists
                if (!file_exists(storage_path('app/public/profile_pictures'))) {
                    mkdir(storage_path('app/public/profile_pictures'), 0777, true);
                }

                // Store the image in the profile_pictures directory
                $imagePath = $request->file('profile_picture')
                    ->store('profile_pictures', 'public');
                
                Log::info('Stored image path: ' . $imagePath);

                // Get just the filename from the path
                $filename = basename($imagePath);

                // Update user's profile picture
                $user->profile_picture = $filename;
                $user->save();

                $fullUrl = asset('storage/profile_pictures/' . $filename);
                Log::info('Full URL: ' . $fullUrl); // Debug log

                return response()->json([
                    'status' => 'success',
                    'message' => 'Profile picture updated successfully',
                    'path' => $filename,
                    'url' => $fullUrl
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'No file uploaded'
            ], 400);

        } catch (\Exception $e) {
            Log::error('Profile picture upload error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload profile picture: ' . $e->getMessage()
            ], 500);
        }
    }
} 