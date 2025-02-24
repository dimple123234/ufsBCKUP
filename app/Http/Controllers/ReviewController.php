<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $review = Review::create([
            'user_id' => Auth::id(),
            'message' => $request->message,
            'rating' => $request->rating,
        ]);

        return response()->json([
            'success' => true,
            'review' => $review->load('user')
        ]);
    }

    public function index()
    {
        $reviews = Review::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($reviews->map(function ($review) {
            return [
                'id' => $review->id,
                'message' => $review->message,
                'rating' => $review->rating,
                'created_at' => $review->created_at,
                'user' => [
                    'id' => $review->user->id,
                    'name' => $review->user->name,
                    'profile_picture_url' => $review->user->profile_picture 
                        ? asset('storage/profile_pictures/' . $review->user->profile_picture)
                        : asset('images/admin.png')
                ]
            ];
        }));
    }
}
